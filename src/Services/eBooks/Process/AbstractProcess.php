<?php

namespace AlfaomegaEbooks\Services\eBooks\Process;

use AlfaomegaEbooks\Services\eBooks\Service;

/**
 * AbstractProcess Class
 * This abstract class provides a blueprint for creating specific eBook processing classes.
 * It implements the ProcessContract interface, which ensures that all child classes have the required methods for processing eBooks.
 * The class also provides a property and a method for controlling whether a product should be updated during the processing of an eBook.
 *
 * @package AlfaomegaEbooks\Services\eBooks\Process
 */
abstract class AbstractProcess implements ProcessContract
{
    /**
     * @var bool $updateProduct
     *
     * A boolean property that determines whether the product should be updated or not.
     * By default, this property is set to true, meaning the product will be updated.
     */
    protected bool $updateProduct = true;

    /**
     * AbstractProcess constructor.
     *
     * Initializes the process with the provided settings.
     *
     * @param array $settings The settings for the process. These settings can include various configuration options.
     */
    public function __construct(
        protected array $settings
    ) {}

    /**
     * Set the value of the $updateProduct property.
     *
     * This method allows you to control whether the product should be updated or not.
     * By default, the product will be updated.
     *
     * @param bool $updateProduct Optional. Whether to update the product. Default is true.
     * @return self Returns the current instance of the class, to allow for method chaining.
     */
    public function setUpdateProduct(bool $updateProduct = true): self
    {
        $this->updateProduct = $updateProduct;
        return $this;
    }

    /**
     * Get the product eBooks.
     * This method takes an array of product IDs as input and returns an array with the product data and the eBook data.
     * It retrieves the product data from WooCommerce and the eBook data from the Alfaomega eBooks API.
     *
     * @param array $data An array of product IDs.
     *
     * @return array|null An array with the product and eBook data. If no data is found, it returns null.
     * @throws \Exception
     */
    protected function getProductEbooks(array $data = []): ?array
    {
        $ebookService = Service::make()->ebooks()->ebookPost();
        $products = [];
        $isbns = [];

        // get products isbn and ebook_isbn
        foreach ($data as $productId) {
            $product = wc_get_product($productId);
            if ($product) {
                $isbn = $product->get_sku();
                $ebookIsbn = $product->get_meta('alfaomega_ebooks_ebook_isbn') ?? null;
                $ebookPost = $ebookService->search($ebookIsbn);
                if (empty($ebookPost)) {
                    $isbns[$ebookIsbn ?? $product->get_sku()] = $productId;
                    $ebookPost = null;
                }
                $products[$productId] = [
                    'isbn'      => $isbn,
                    'ebookIsbn' => $ebookIsbn,
                    'ebookPost' => array_merge($ebookPost, ['product_id' => $productId]),
                ];
            }
        }
        if (empty($products)) {
            return null;
        }

        return $this->getEbooksInformation($isbns, $products);
    }

    /**
     * Get the books information.
     * This method takes an array of ISBNs and an array of product data as input and returns an array with the product
     * data and the ISBN data. It retrieves the book information from the Alfaomega eBooks API and updates the product
     * data with the book information.
     *
     * @param array $isbns    An array of ISBNs.
     * @param array $products An array of product data.
     *
     * @return array An array with the updated product data and the ISBN data.
     * @throws \Exception
     */
    protected function getEbooksInformation(array $isbns, array $products): array
    {
        $ebookService = Service::make()->ebooks()->ebookPost();
        if (!empty($isbns)) {
            $ebooks = $ebookService->index(array_keys($isbns));
            if ($ebooks) {
                foreach ($ebooks as $ebook) {
                    if (!empty($isbns[$ebook['isbn']])) {
                        $productId = $isbns[$ebook['isbn']];
                    } else {
                        $productId = $isbns[$ebook['printed_isbn']] ?? null;
                    }
                    $ebookPost = $ebookService->updateOrCreate(null, $ebook);
                    $products[$productId]['ebookPost'] = array_merge($ebookPost, ['product_id' => $productId]);
                }
            }
        }

        return $products;
    }

    /**
     * Link the product eBooks.
     * @param array $products
     *
     * @return void
     * @throws \Exception
     */
    protected function linkProductEbooks(array $products): void
    {
        $productService = Service::make()->wooCommerce()->linkProduct();
        foreach ($products as $product) {
            $productService->single($product['ebookPost'], false);
        }
    }
}
