# Keyword Stuffing class
This class helps to detect and remove keyword stuffing from a text.

<a name="install"></a>
## Installation

```
$ composer require citco/keyword-stuffing
```

```json
{
    "require": {
        "citco/keyword-stuffing": "*"
    }
}
```

```php
<?php
require './vendor/autoload.php';

use Citco\KeywordStuffing;

$ks = new KeywordStuffing;
```

<a name="sample-code"></a>
### Sample code

Here are some samples on using this class:
```php
$text_1 = $text_2 = <<<EOT
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dictum in diam eu tristique. Mauris vel leo nec ex efficitur sodales at vitae nisi. Nulla facilisi. Integer consectetur vitae velit in vehicula. In fringilla justo a vehicula tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse vitae tempus nisl. Quisque sit amet aliquet libero. Integer eu elementum ligula. Nam feugiat dolor at diam tempus, at auctor quam tempor. Duis sed volutpat libero, nec dignissim justo. Nam nec eros ultricies, ultricies ex vel, accumsan libero. Aenean eleifend sed metus a lobortis. Etiam eu pellentesque dolor.<br>
Curabitur sit amet congue nulla. Fusce tincidunt aliquam placerat. Phasellus consequat faucibus ex. Cras vel mauris vitae nibh semper finibus. Praesent dictum vestibulum turpis, non aliquam nisl. Donec lorem turpis, pellentesque id vehicula sit amet, efficitur vitae libero. Mauris at sapien nisl. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.<br>
Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword Keyword<br>
Maecenas tempus maximus odio id facilisis. Nulla sit amet efficitur magna. Ut quam nibh, malesuada eget facilisis at, porttitor ut sapien. Duis vitae mattis nunc. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus ultrices placerat lectus. Phasellus placerat orci at elit tincidunt, fringilla porta purus consequat. Donec tristique, enim id porta semper, mi risus vulputate dolor, et facilisis turpis ante eget nisl. Duis eget justo dolor. Nulla quis mi at eros sagittis dignissim. Curabitur vitae nibh ligula.<br>
Morbi pretium ultrices ex, eu luctus tellus vulputate eget. Praesent quis enim non quam malesuada ullamcorper. Aliquam sed libero id quam vulputate luctus. In dapibus ipsum id libero elementum, vitae pulvinar tellus consequat. Donec quis tempor nunc. Proin pharetra lacus iaculis, tincidunt nulla non, hendrerit felis. Nulla facilisis elementum posuere. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Proin a quam sit amet elit maximus porta ac vel elit.<br>
Morbi laoreet, neque ac pharetra placerat, ex risus condimentum enim, mattis porttitor ex augue vel mauris. Morbi felis elit, pulvinar in interdum vel, tincidunt sed ex. Integer feugiat tempus odio vel dignissim. Phasellus pulvinar libero at sem tincidunt sodales. Phasellus tincidunt auctor tortor, non accumsan mauris venenatis vitae. Etiam in vulputate ipsum, eget volutpat sem. Etiam eu faucibus nisi. Donec volutpat, justo vel pellentesque convallis, mi est pulvinar risus, vitae rhoncus turpis turpis quis felis. In tempor tellus non molestie rhoncus. Sed ut ligula leo. Ut aliquam elit a tortor aliquet, nec faucibus velit dignissim. Morbi posuere dignissim urna id pretium. Sed rutrum sollicitudin diam ut suscipit.<br>
Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing Keyword stuffing<br>
EOT;

// Sample #1
$ks = new KeywordStuffing($text_1);
$text = $ks->removeKeywordStuffing();
$summary = $ks->getSummary();

// Sample #2
$ks = new KeywordStuffing();
$text = $ks->removeKeywordStuffing($text_1);
$summary = $ks->getSummary();
$text = $ks->removeKeywordStuffing($text_2);
$summary = $ks->getSummary();

// Sample #3
$ks = new KeywordStuffing();
$summary = $ks->getSummary($text_1);
$text = $ks->removeKeywordStuffing();

// Sample #4
$text = (new KeywordStuffing)->removeKeywordStuffing($text_1);
```

<a name="issues"></a>
### Issues
Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/citco/keyword-stuffing/issues).

<a name="requirements"></a>
### Requirements

PHP 5.4 or above

<a name="license"></a>
### License

This source code is licensed under the MIT License - see the LICENSE file for details
