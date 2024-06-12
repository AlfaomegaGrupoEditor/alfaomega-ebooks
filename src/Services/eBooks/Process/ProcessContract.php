<?php

namespace AlfaomegaEbooks\Services\Process;

interface ProcessContract
{
    /**
     * Do the process on a single object.
     *
     * @param array $eBook
     * @param bool $throwError
     * @param int|null $postId
     *
     * @return void
     * @throws \Exception
     */
    public function single(array $eBook, bool $throwError=false, int $postId = null): void;

    /**
     * Do the process in bach.
     *
     * @param array $data The data.
     *
     * @return array
     */
    public function batch(array $data = []): array;
}
