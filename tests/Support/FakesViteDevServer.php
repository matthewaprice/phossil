<?php

namespace Phossil\Tests\Support;

/**
 * Points the app at a stand-in phossil directory whose `hot` file names a Vite
 * dev server, so vite() writes the same tags whether or not the vite container
 * runs. The real img/ is linked in, so icons and asset() still resolve.
 *
 * @author Matthew A Price
 */
trait FakesViteDevServer
{
    /** The real phossil directory, restored by stopFakeViteDevServer(). */
    private string $real_root;

    /** The stand-in phossil directory. */
    private string $fake_root;

    protected function startFakeViteDevServer(): void
    {
        global $app;

        $this->real_root = $app->root;
        $this->fake_root = sys_get_temp_dir() . '/phossil-test-' . bin2hex(random_bytes(4));

        mkdir($this->fake_root);
        symlink($this->real_root . '/img', $this->fake_root . '/img');
        file_put_contents($this->fake_root . '/hot', 'http://localhost:5173');

        $app->root = $this->fake_root;
    }

    protected function stopFakeViteDevServer(): void
    {
        global $app;

        $app->root = $this->real_root;

        unlink($this->fake_root . '/hot');
        unlink($this->fake_root . '/img');
        rmdir($this->fake_root);
    }
}
