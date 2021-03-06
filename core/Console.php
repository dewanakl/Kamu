<?php

namespace Core;

/**
 * Saya console untuk mempermudah develop app
 *
 * @class Console
 * @package Core
 */
final class Console
{
    /**
     * Perintah untuk eksekusi
     * 
     * @var string|null $command
     */
    private string|null $command;

    /**
     * Perintah untuk eksekusi
     * 
     * @var string $command
     */
    private $options;

    /**
     * Waktu yang dibutuhkan
     * 
     * @var int $timenow
     */
    private $timenow;

    /**
     * Text warna hijau
     * 
     * @var string $green
     */
    private $green = "\033[32m";

    /**
     * Text warna kuning
     * 
     * @var string $yellow
     */
    private $yellow = "\033[33m";

    /**
     * Text warna biru muda
     * 
     * @var string $cyan
     */
    private $cyan = "\033[36m";

    /**
     * Text warna semula
     * 
     * @var string $normal
     */
    private $normal = "\033[37m";

    /**
     * Buat objek session
     *
     * @return void
     */
    function __construct($argv)
    {
        if (PHP_MAJOR_VERSION < 8) {
            $this->exception('Minimal PHP 8 !');
        }

        array_shift($argv);
        $this->command = $argv[0] ?? null;
        $this->options = $argv[1] ?? null;
        $this->timenow = startTime;

        print($this->green . "Kamu PHP Framework v1.0\n");
        print($this->yellow . "Saya Console v1.0\n\n");
        print($this->normal);
    }

    /**
     * Tampilkan pesan khusus error
     *
     * @param string $message
     * @param bool $fail
     * @param ?string $options
     * @return void
     */
    private function exception(string $message, bool $fail = true, ?string $options = null): void
    {
        if ($fail) {
            exit("\033[31m$message\033[37m");
        }

        if ($options) {
            echo "\n$options\n";
        }
    }

    /**
     * Kalkulasi waktu yang dibutuhkan
     *
     * @return string
     */
    private function executeTime(): string
    {
        $result = '(' . floor(number_format(microtime(true) - $this->timenow, 3, '')) . ' ms)';
        $this->timenow = microtime(true);
        return $this->cyan . $result . $this->normal;
    }

    /**
     * Migrasi ke database
     *
     * @param bool $up
     * @return void
     */
    private function migrasi(bool $up = true): void
    {
        $baseFile = __DIR__ . '/../database/schema/';

        $files = scandir($baseFile, ($up) ? 0 : 1);
        $files = array_diff($files, array('..', '.'));

        foreach ($files as $file) {
            $arg = include $baseFile . $file;
            ($up) ? $arg->up() : $arg->down();
            $info = ($up) ? $this->green . ' Migrasi ' : $this->yellow . ' Migrasi kembali ';
            print("\n" . $file . $info . $this->normal . $this->executeTime());
        }
    }

    /**
     * Isi nilai ke database
     *
     * @return void
     */
    private function generator(): void
    {
        $arg = include __DIR__ . '/../database/generator.php';
        $arg->run();
        print("\nGenerator" . $this->green . " berhasil " . $this->normal . $this->executeTime());
    }

    /**
     * Buat file migrasi
     *
     * @param ?string $name
     * @return void
     */
    private function createMigrasi(?string $name): void
    {
        $this->exception('Butuh Nama file !', !$name);
        $data = require_once __DIR__ . '/../helpers/templateMigrasi.php';
        $data = str_replace('NAME', $name, $data);
        $result = file_put_contents(__DIR__ . '/../database/schema/' . strtotime('now') . '_' . $name . '.php', $data);
        $this->exception('Gagal membuat migrasi', !$result, 'Berhasil membuat migrasi');
    }

    /**
     * Buat file middleware
     *
     * @param ?string $name
     * @return void
     */
    private function createMiddleware(?string $name): void
    {
        $this->exception('Butuh Nama file !', !$name);
        $data = require_once __DIR__ . '/../helpers/templateMiddleware.php';
        $data = str_replace('NAME', $name, $data);
        $result = file_put_contents(__DIR__ . '/../middleware/' . $name . '.php', $data);
        $this->exception('Gagal membuat middleware', !$result, 'Berhasil membuat middleware');
    }

    /**
     * Buat file controller
     *
     * @param ?string $name
     * @return void
     */
    private function createController(?string $name): void
    {
        $this->exception('Butuh Nama file !', !$name);
        $data = require_once __DIR__ . '/../helpers/templateController.php';
        $data = str_replace('NAME', $name, $data);
        $result = file_put_contents(__DIR__ . '/../controllers/' . $name . '.php', $data);
        $this->exception('Gagal membuat controller', !$result, 'Berhasil membuat controller');
    }

    /**
     * Buat file model
     *
     * @param ?string $name
     * @return void
     */
    private function createModel(?string $name): void
    {
        $this->exception('Butuh Nama file !', !$name);
        $data = require_once __DIR__ . '/../helpers/templateModel.php';
        $data = str_replace('NAME', $name, $data);
        $data = str_replace('NAMe', strtolower($name), $data);
        $result = file_put_contents(__DIR__ . '/../models/' . $name . '.php', $data);
        $this->exception('Gagal membuat model', !$result, 'Berhasil membuat model');
    }

    /**
     * Tampilkan list menu yang ada
     *
     * @return void
     */
    private function listMenu(): void
    {
        $menus = [
            [
                'command' => 'coba',
                'description' => 'jalankan php dengan virtual server'
            ],
            [
                'command' => 'migrasi',
                'description' => 'bikin tabel di database kamu [gen]'
            ],
            [
                'command' => 'migrasi:kembali',
                'description' => 'kembalikan seperti awal databasenya'
            ],
            [
                'command' => 'migrasi:segar',
                'description' => 'kembalikan seperti awal dan isi ulang [gen]'
            ],
            [
                'command' => 'generator',
                'description' => 'isi nilai ke database'
            ],
            [
                'command' => 'bikin:migrasi',
                'description' => 'bikin file migrasi [nama file]'
            ],
            [
                'command' => 'bikin:middleware',
                'description' => 'bikin file middleware [nama file]'
            ],
            [
                'command' => 'bikin:controller',
                'description' => 'bikin file controller [nama file]'
            ],
            [
                'command' => 'bikin:model',
                'description' => 'bikin file model [nama file]'
            ],
        ];

        print("Penggunaan:\n perintah [options]\n\n");
        $mask = $this->cyan . "%-20s" . $this->normal . " %-30s\n";

        foreach ($menus as $value) {
            printf($mask, $value['command'], $value['description']);
        }
    }

    /**
     * Jalankan console
     *
     * @return int
     */
    public function run(): int
    {
        switch ($this->command) {
            case 'coba':
                $location = ($this->options) ? $this->options : 'localhost';
                shell_exec("php -S $location:8000 -t public");
                break;
            case 'migrasi':
                $this->migrasi();
                if ($this->options == '--gen') {
                    $this->generator();
                }
                break;
            case 'generator':
                $this->generator();
                break;
            case 'migrasi:kembali':
                $this->migrasi(false);
                break;
            case 'migrasi:segar':
                $this->migrasi(false);
                $this->migrasi();
                if ($this->options == '--gen') {
                    $this->generator();
                }
                break;
            case 'bikin:migrasi':
                $this->createMigrasi($this->options);
                break;
            case 'bikin:middleware':
                $this->createMiddleware($this->options);
                break;
            case 'bikin:controller':
                $this->createController($this->options);
                break;
            case 'bikin:model':
                $this->createModel($this->options);
                break;
            default:
                $this->listMenu();
                break;
        }

        return 0;
    }
}
