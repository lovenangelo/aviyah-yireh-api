<?php

namespace App\Exports;

abstract class BaseExporter
{
    protected $data;
    protected $fileName;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set the name of the file to be exported.
     */
    public function setFileName(string $name): self
    {
        $this->fileName = $name;
        return $this;
    }

    /**
     * Set the data to be exported.
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the data to be exported.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the name of the file to be exported.
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Export the data (must be implemented in child class).
     */
    abstract public function export(): mixed;
}
