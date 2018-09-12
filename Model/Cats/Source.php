<?php

namespace Kokoc\Demo\Model\Cats;

class Source
{
    private $website = 'https://www.buzzfeed.com/expresident/best-cat-pictures';

    public function getCats()
    {
        $content = file_get_contents($this->website);

        preg_match_all('~subbuzz subbuzz-image[^>]+.*?subbuzz__number">(\d+)\..*?js-subbuzz__title-text">([^<]+).*?subbuzz__media-image.*?data-src="([^"]+).*?subbuzz__description.*?<p>(.*?)</p>~si', $content, $matches);

        $result = [];

        foreach ($matches[1] as $offset => $price) {
            $result[] = [
                'price' => $price,
                'name' => $matches[2][$offset],
                'image' => $matches[3][$offset],
                'description' => $matches[4][$offset]
            ];
        }

        return $result;
    }
}