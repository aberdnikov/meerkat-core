<?php
    class Kohana_Controller_Sitemap extends Controller {
        function action_index() {
            $cache = Cache::instance();
            //$cache->delete('sitemap');
            if (!($response = $cache->get('sitemap'))) {
                $collection = new \Sitemap\Collection;
                for($i=1;$i<11;$i++){
                    $entry= new \Sitemap\Sitemap\SitemapEntry;
                    $entry->setLocation('http://example.com/page-'.$i);
                    $entry->setLastMod(date('r', time()));
                    $entry->setPriority(1);
                    $entry->setChangeFreq('monthly');
                    $collection->addSitemap($entry);
                }
                $collection->setFormatter(new \Sitemap\Formatter\XML\SitemapIndex);
                $response = $collection->output();
                // Cache the output for 24 hours.
                $cache->set('sitemap', $response, 86400);
            }
            // Output the sitemap.
            header('Content-Type: application/xml');
            exit($response);
        }
    }