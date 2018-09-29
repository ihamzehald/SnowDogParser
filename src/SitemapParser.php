<?php
/**
 * @author Hamza al Darawsheh 29 Sep 2018 <ihamzehald@gmail.com>
 * A sitemap parser as a part of Snowdog php test task6
 * Ticket Ref: task_6
 */

namespace SnowDog\Tools;

    class SitemapParser
{
    /**
     * @author Hamza al Darawsheh 29 Sep 2018 <ihamzehald@gmail.com>
     * @param $filePath as full absolute file path of the xml file
     * $filePath example : /var/www/html/test-sitemap.xml
     * @return array as parsed sitemap data
     * Output sample:
     * Array
     *   (
     *      [host] => www.example.com
     *      [urls] => Array
     *          (
     *              [0] => Array
     *                  (
     *                      [loc] => http://www.example.com/catalog?item=12&desc=vacation_hawaii
     *                      [lastmod] => 2005-01-01
     *                      [changefreq] => monthly
     *                      [priority] => 0.8
     *                      [url_parts] => Array
     *                          (
     *                              [scheme] => http
     *                              [host] => www.example.com
     *                              [path] => /catalog
     *                              [query] => item=12&desc=vacation_hawaii
     *                          )
     *                  )
     *              ..........
     *          )
     *      [errors_list] => array()
     *  )
     *
     */
    private function coreXMLParser($filePath)
    {
        $xmlData = ['host' => '', 'urls' => [], "errors_list" => []];

        libxml_use_internal_errors(true);

        $xml = simplexml_load_file($filePath);

        if ($xml) {

            //convert SimpleXML objects to array
            $xmlArray = $arr = json_decode(json_encode($xml), 1);
            $xmlUrls = $xmlArray['url'];
            $mainWebsiteParts = $this->getUrlParts($xmlUrls[0]);
            //die(print_r($mainWebsiteParts));
            $xmlData['host'] = !empty($mainWebsiteParts['host']) ? $mainWebsiteParts['host'] : trim($mainWebsiteParts['path'], '/\\');

            //create url_parts for each url and store url parts in it

            $finalUrls = [];

            foreach ($xmlUrls as $url) {
                $url['url_parts'] = $this->getUrlParts($url);
                $finalUrls[] = $url;
            }

            $xmlData['urls'] = $finalUrls;

        } else {
            //Errors happened while trying to read xml file

            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                $xmlData['errors_list'][] = $error->message;
            }

            libxml_clear_errors();

        }

        return $xmlData;
    }

    private function coreJSONParser($filePath)
    {
        #TODO: implement core parser for JSON files
        throw new Exception("Not implemented yet.");
    }

    private function coreCSVParser($filePath)
    {
        #TODO: implement core parser for CSV files
        throw new Exception("Not implemented yet.");
    }

    /**
     * @author Hamza al Darawsheh 29 Sep 2018 <ihamzehald@gmail.com>
     * @param $url as the a url array from SimpleXML object
     * @return array as all the parts of a url
     * Output sample:
     * Array
     * (
     *      [scheme] => http
     *      [host] => www.example.com
     *      [path] => /catalog
     *      [query] => item=12&desc=vacation_hawaii
     * )
     */
    private function getUrlParts($url)
    {
        $urlParts = parse_url($url['loc']);
        return $urlParts;
    }

    /**
     * @author Hamza al Darawsheh 29 Sep 2018 <ihamzehald@gmail.com>
     * @param $type as the suppported file type of the sitemap (xml, JSON, csv)
     * @param $filePath as full absolute file path of the xml file
     * @return array|void as the parsed sitemap result
     */
    public function fileParser($type, $filePath)
    {
        switch ($type) {
            case $type == 'XML' or $type == 'xml':
                return $this->coreXMLParser($filePath);
                break;

            case $type == 'JSON' or $type == 'json':
                return $this->coreJSONParser($filePath);
                break;

            case $type == 'csv':
                return $this->coreCSVParser($filePath);
                break;

            default:

                throw new Exception("This type not supported yet, the supported types are XML.");

        }
    }

}
