<?php

namespace Phossil\Services;

use Phossil\Phossil;
use Phossil\Services\Router\{
    Route,
    RouteType
};
use RuntimeException;

/**
 * Renders every route to a static HTML file and copies the assets beside them.
 *
 * Pages are fetched over HTTP from the running nginx container, so the build
 * gets exactly what a browser would, and written to dist/ as
 * `<route>/index.html`, ready for any static host.
 */
class Build {

    protected string $app_dir;
    protected string $dist_dir;

    public function __construct() {

        $this->app_dir = Phossil::root();
        // dist/ sits next to the app folder.
        $this->dist_dir = dirname( $this->app_dir ) . '/dist';

    }

    public function routes() {

        $this->destroy();

        foreach( $this->urls() as $route ) {
            $file_name = $route === '/' ? '/index.html' : $route . '/index.html';
            $this->page( $route, $file_name );
        }

    }

    /**
     * Writes the 404 page to 404.html, the name most static hosts look for.
     */
    public function notFound() {

        $this->page( '/404', '/404.html', 404 );

    }

    /**
     * Turns the route definitions into the URLs to build, leaving out any marked `skip_build`.
     *
     * @return list<string> URL paths such as '/' and '/about'.
     */
    public function urls(): array {

        return array_values( array_unique( array_map(
            fn( RouteType $route ) => '/' . $route->path,
            array_filter( Route::$routes, fn( RouteType $route ) => !$route->skip_build )
        ) ) );

    }

    /**
     * Copies img/ into the build.
     */
    public function images() {

        $source_img = $this->app_dir . '/img';
        $dest_img = $this->dist_dir . '/' . $this->assets() . '/img';
        if ( file_exists( $source_img ) ) {
            $this->copyDirectory( $source_img, $dest_img );
        }

    }

    /**
     * Copies Vite's CSS and JS from the app folder's dist/ into the build. Hidden files are
     * skipped, so the manifest in .vite/ stays behind.
     */
    public function vite() {

        $source_dist = $this->app_dir . '/dist';
        if ( !is_dir( $source_dist ) ) {
            throw new RuntimeException( "No Vite build in {$source_dist}/. Run `pnpm build` first." );
        }
        $this->copyDirectory( $source_dist, $this->dist_dir );

    }

    /**
     * Fetches one URL from nginx and writes the response into the build.
     *
     * @param string $route         URL path to request, e.g. '/about'.
     * @param string $file_name     Where to write it, relative to dist/.
     * @param int    $expected_code The HTTP status the page should answer with.
     */
    protected function page( string $route, string $file_name, int $expected_code = 200 ) {

        // nginx is reachable by its compose service name on the shared network.
        $url = 'http://nginx' . rtrim( $route, '/' ) . '/';
        // ignore_errors returns the body of a 404 too, so the error page can be built.
        // X-Static-Build makes vite() link the built files, even while the dev server runs.
        $context = stream_context_create( [ 'http' => [
            'ignore_errors' => true,
            'header' => 'X-Static-Build: 1',
        ] ] );
        $page_html = @file_get_contents( $url, false, $context );
        $status = $this->status( $http_response_header ?? [] );

        if ( $page_html === false || $status !== $expected_code ) {
            throw new RuntimeException( "Could not build route {$route}: {$url} answered " . ( $status ?: 'nothing' ) . ", expected {$expected_code}." );
        }

        $file_destination = $this->dist_dir . $file_name;
        $this->checkDir( $file_destination );
        file_put_contents( $file_destination, $page_html );

        echo "Built route {$route} -> {$file_name}\n";

    }

    /**
     * Reads the status code from the last response's headers, after any redirects.
     *
     * @param list<string> $headers The `$http_response_header` from the request.
     * @return int The status code, or 0 when there was no response.
     */
    protected function status( array $headers ): int {

        $status = 0;
        foreach ( $headers as $header ) {
            if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', $header, $matches ) ) {
                $status = (int) $matches[1];
            }
        }

        return $status;

    }

    /**
     * Where our assets go in the build, matching the URL that asset() writes into the pages.
     *
     * @return string The `assets` config value without its leading slash, e.g. 'assets'.
     */
    protected function assets(): string {

        return ltrim( Phossil::config( 'assets' ), '/' );

    }

    public function checkDir( string $file_path ) {

        $dest_dir = dirname( $file_path );
        if ( !is_dir( $dest_dir ) ) {
            mkdir( $dest_dir, 0755, true );
        }

    }

    /**
     * Copies a directory recursively, leaving out hidden files such as .DS_Store.
     */
    public function copyDirectory( string $source, string $destination ) {
        if ( !is_dir( $source ) ) {
            return false;
        }

        if ( !is_dir( $destination ) ) {
            mkdir( $destination, 0755, true );
        }

        $dir = opendir( $source );
        while ( false !== ( $file = readdir( $dir ) ) ) {
            if ( $file[0] !== '.' ) {
                $src_path = $source . '/' . $file;
                $dest_path = $destination . '/' . $file;

                if ( is_dir( $src_path ) ) {
                    $this->copyDirectory( $src_path, $dest_path );
                } else {
                    copy( $src_path, $dest_path );
                }
            }
        }
        closedir( $dir );
        return true;
    }

    /**
     * Empties dist/ when a previous build left one. page() creates it again as it writes.
     */
    public function destroy() {

        if ( is_dir( $this->dist_dir ) ) {
            foreach ( scandir( $this->dist_dir ) as $item ) {
                if ( $item != '.' && $item != '..' ) {
                    $this->remove( $this->dist_dir . '/' . $item );
                }
            }
        }

    }

    protected function remove( string $path ) {

        if ( !is_dir( $path ) ) {
            unlink( $path );
            return;
        }

        foreach ( scandir( $path ) as $item ) {
            if ( $item != '.' && $item != '..' ) {
                $this->remove( $path . '/' . $item );
            }
        }

        rmdir( $path );

    }

}
