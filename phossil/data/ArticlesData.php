<?php

namespace Phossil\Data;

class ArticlesData
{
    public static function getAll(): array
    {
        return [
            [
                'title' => 'Article #1',
                'slug' => 'article-1',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut gravida sem eget dui venenatis faucibus. Proin congue, nisl eu mattis sollicitudin, felis leo molestie eros, ut blandit orci sem nec nisl. Nullam vestibulum tristique orci vel dapibus. Donec interdum, velit pulvinar pulvinar luctus, turpis nisl blandit est, a rutrum velit nulla at mauris. Quisque et egestas quam. In in massa sed dui mattis ullamcorper. Morbi at mollis metus, sit amet eleifend dolor. Praesent ut tellus dictum, bibendum lorem sit amet, feugiat justo. Duis maximus orci enim, a dictum mauris porta at. Vivamus pellentesque dolor non mauris ornare ultricies. Aliquam pretium diam est, convallis elementum arcu dapibus at. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'
            ],[
                'title' => 'Article #2',
                'slug' => 'article-2',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed interdum quam sed lorem dictum, eget placerat arcu condimentum. Duis malesuada vitae ex at euismod. Ut eu tellus quis ligula iaculis dignissim. In lacus urna, cursus ac egestas sed, hendrerit vel sapien. Morbi ligula erat, imperdiet ut sem convallis, semper rutrum justo. Ut rutrum ipsum vitae elementum varius. Donec molestie finibus nibh. Mauris a velit lacinia, mollis massa id, venenatis augue. Nam eleifend, libero vel tristique iaculis, nibh sapien lacinia dolor, interdum sollicitudin ex tellus id lacus. Curabitur elementum sapien non auctor efficitur. Praesent eleifend vitae magna ut iaculis.'
            ]
        ];
    }
}
