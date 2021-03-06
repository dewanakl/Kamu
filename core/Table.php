<?php

namespace Core;

/**
 * Membuat tabel dengan mudah
 * 
 * @class Table
 * @package Core
 */
class Table
{
    /**
     * Param query
     * 
     * @var array $query
     */
    private array $query = array();

    /**
     * Tipe dbms
     * 
     * @var string $type
     */
    private string $type;

    /**
     * Nama tabelnya
     * 
     * @var string $table
     */
    private string $table;

    /**
     * Init objek
     *
     * @return void
     */
    function __construct()
    {
        $this->type = env('DB_DRIV');
    }

    /**
     * Set nama table di database
     *
     * @param string $name
     * @return void
     */
    public function table(string $name): void
    {
        $this->table = $name;
    }

    /**
     * Export hasilnya ke string sql
     * 
     * @return string
     */
    public function export(): string
    {
        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (';
        $query .= join(", ", $this->query);
        $query .= ');';
        $this->query = array();

        return $query;
    }

    /**
     * Get index paling akhir
     * 
     * @return int
     */
    private function getLastArray(): int
    {
        return count($this->query) - 1;
    }

    /**
     * Id, unique, primary key
     * 
     * @param string $name
     * @return void
     */
    public function id(string $name = 'id'): void
    {
        if ($this->type == 'pgsql') {
            $this->query[] = "$name SERIAL NOT NULL PRIMARY KEY";
        } else {
            $this->query[] = "$name INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT";
        }
    }

    /**
     * Int non primary key
     * 
     * @param string $name
     * @return void
     */
    public function unsignedInteger(string $name): void
    {
        $this->query[] = "$name INT NOT NULL";
    }

    /**
     * Tipe string atau varchar
     * 
     * @param string $name
     * @param int $len
     * @return self
     */
    public function string(string $name, int $len = 255): self
    {
        $this->query[] = "$name VARCHAR($len) NOT NULL";
        return $this;
    }

    /**
     * Tipe integer
     * 
     * @param string $name
     * @return self
     */
    public function integer(string $name): self
    {
        if ($this->type == 'pgsql') {
            $this->query[] = "$name bigint NOT NULL";
        } else {
            $this->query[] = "$name INTEGER(11) NOT NULL";
        }

        return $this;
    }

    /**
     * Tipe text
     * 
     * @param string $name
     * @return self
     */
    public function text(string $name): self
    {
        $this->query[] = "$name TEXT NOT NULL";
        return $this;
    }

    /**
     * Tipe timestamp / datetime
     * 
     * @param string $name
     * @return self
     */
    public function dateTime(string $name): self
    {
        if ($this->type == 'pgsql') {
            $this->query[] = "$name timestamp without time zone NOT NULL";
        } else {
            $this->query[] = "$name datetime NOT NULL";
        }

        return $this;
    }

    /**
     * Create_at and update_at
     * 
     * @return void
     */
    public function timeStamp(): void
    {
        if ($this->type == 'pgsql') {
            $this->query[] = "create_at timestamp without time zone NOT NULL DEFAULT NOW()";
            $this->query[] = "update_at timestamp without time zone NOT NULL DEFAULT NOW()";
        } else {
            $this->query[] = "create_at datetime NOT NULL DEFAULT NOW()";
            $this->query[] = "update_at datetime NOT NULL DEFAULT NOW()";
        }
    }

    /**
     * Boleh kosong
     * 
     * @return self
     */
    public function nullable(): self
    {
        $this->query[$this->getLastArray()] = str_replace('NOT NULL', 'NULL', end($this->query));
        return $this;
    }

    /**
     * Default value pada dbms
     * 
     * @param string|int $name
     * @return void
     */
    public function default(string|int $name): void
    {
        if (is_string($name)) {
            $constraint = " DEFAULT '$name'";
        } else {
            $constraint = " DEFAULT $name";
        }

        $this->query[$this->getLastArray()] = end($this->query) . $constraint;
    }

    /**
     * Harus berbeda
     * 
     * @return void
     */
    public function unique(): void
    {
        $this->query[$this->getLastArray()] = end($this->query) . ' UNIQUE';
    }

    /**
     * Bikin relasi antara nama attribute
     * 
     * @param string $name
     * @return self
     */
    public function foreign(string $name): self
    {
        $this->query[] = "CONSTRAINT FK_$name FOREIGN KEY($name)";
        return $this;
    }

    /**
     * Dengan nama attribute tabel targetnya
     * 
     * @param string $name
     * @return self
     */
    public function references(string $name): self
    {
        $this->query[$this->getLastArray()] = end($this->query) . " REFERENCES TABLE-TARGET($name)";
        return $this;
    }

    /**
     * Nama tabel targetnya
     * 
     * @param string $name
     * @return self
     */
    public function on(string $name): self
    {
        $this->query[$this->getLastArray()] = str_replace('TABLE-TARGET', $name, end($this->query));
        return $this;
    }

    /**
     * Hapus nilai pada foreign key juga jika menghapus
     * 
     * @return void
     */
    public function cascadeOnDelete(): void
    {
        $this->query[$this->getLastArray()] = end($this->query) . ' ON DELETE CASCADE';
    }
}
