<?php

namespace App\Tests\Support;

use Phossil\Phossil;

/**
 * Points the app at a stand-in app folder whose `hot` file names a Vite
 * dev server, so vite() writes the same tags whether or not the vite container
 * runs. The real img/ is linked in, so asset() still resolves.
 *
 * @author Matthew A Price
 */
trait FakesViteDevServer
{
    /** The real app folder, restored by stopFakeViteDevServer(). */
    private string $real_root;

    /** The stand-in app folder. */
    private string $fake_root;

    protected function startFakeViteDevServer(): void
    {
        $this->real_root = Phossil::root();
        $this->fake_root = sys_get_temp_dir() . '/phossil-test-' . bin2hex(random_bytes(4));

        mkdir($this->fake_root);
        symlink($this->real_root . '/img', $this->fake_root . '/img');
        file_put_contents($this->fake_root . '/hot', 'http://localhost:5173');

        Phossil::boot($this->fake_root, require $this->real_root . '/config.php');
    }

    protected function stopFakeViteDevServer(): void
    {
        Phossil::boot($this->real_root, require $this->real_root . '/config.php');

        unlink($this->fake_root . '/hot');
        unlink($this->fake_root . '/img');
        rmdir($this->fake_root);
    }
}
