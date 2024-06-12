<?php

namespace AlfaomegaEbooks\Services\Process;

interface ProcessContract
{
    /**
     * Do the process.
     *
     * @return array
     */
    public function single(): array;

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     */
    public function batch(array $data = []): array;
}
