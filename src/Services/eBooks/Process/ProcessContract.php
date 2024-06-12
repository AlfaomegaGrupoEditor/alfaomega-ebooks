<?php

namespace AlfaomegaEbooks\Services\Process;

interface ProcessContract
{
    /**
     * Do the process on a single object.
     *
     * @param array $eBook
     *
     * @return array
     */
    public function single(array $eBook): void;

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     */
    public function batch(array $data = []): array;
}
