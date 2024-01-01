<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service;

class FileService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Returns a list of the files that your account has access to. The files are
     * returned sorted by creation date, with the most recently created files appearing
     * first.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\File>
     *
     * @license MIT
     * Modified by learndash on 09-November-2023 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/files', $params, $opts);
    }

    /**
     * Retrieves the details of an existing file object. Supply the unique file ID from
     * a file, and Stripe will return the corresponding file object. To access file
     * contents, see the <a href="/docs/file-upload#download-file-contents">File Upload
     * Guide</a>.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\File
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/files/%s', $id), $params, $opts);
    }

    /**
     * Create a file.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @return \StellarWP\Learndash\Stripe\File
     */
    public function create($params = null, $opts = null)
    {
        $opts = \StellarWP\Learndash\Stripe\Util\RequestOptions::parse($opts);
        if (!isset($opts->apiBase)) {
            $opts->apiBase = $this->getClient()->getFilesBase();
        }

        // Manually flatten params, otherwise curl's multipart encoder will
        // choke on nested null|arrays.
        $flatParams = \array_column(\StellarWP\Learndash\Stripe\Util\Util::flattenParams($params), 1, 0);

        return $this->request('post', '/v1/files', $flatParams, $opts);
    }
}
