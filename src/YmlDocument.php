<?php

namespace traineratwot\yandexYmlGenerator;


use DomDocument;
use DomElement;
use DOMException;
use DOMImplementation;
use RuntimeException;

class YmlDocument extends DomDocument
{

    /**
     * @var DOMElement|NULL
     */
    public $currencies = null;
    /**
     * @var DOMElement|NULL
     */
    public $categories = null;
    public $offer = NULL;
    /**
     * @var DOMElement|false
     */
    public $shop;
    public $fp;
    public $fname = './out.xml';
    public $bufferSize = NULL;
    /**
     * @var DOMElement|false
     */
    public $root;


    /**
     * @throws DOMException
     */
    public function __construct($name, $company, $enc = "UTF-8", $date = null)        // или windows-1251
    {
        parent::__construct('1.0', $enc);

        $imp = new DOMImplementation();

        $root = $this->createElement('yml_catalog');
        $this->root = $root;// делаем основные элементы
        $shop = $this->createElement('shop');
        $this->shop = $shop;
        $root->setAttribute('date', $date ?: date('Y-m-d H:i'));
        $root->appendChild($shop);
        $this->appendChild($root);
        // TODO: mb_strlen не включены по умолчанию, лучше чем-то заменить
        if (mb_strlen($name, $this->encoding) > 20) {
            throw new RuntimeException("name='$name' длиннее 20 символов");
        }
        if ($name) {
            $this->add('name', $name);
        }
        if ($company) {
            $this->add('company', $company);
        }
    }

    /**
     * @throws DOMException
     */
    public function add($name, $value = FALSE)                                                // добавление элемента к shop
    {
        if ($value !== FALSE) {
            $this->shop->appendChild($this->createElement($name, $value));
        } else {
            $this->shop->appendChild($this->createElement($name));
        }
        return $this;
    }

    public function fileName($fname)
    {
        $this->fname = $fname;
        $this->fp = fopen($this->fname, 'w');
        return $this;
    }

    public function bufferSize($size)
    {
        $this->bufferSize = $size;
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function url($url)
    {
        if (mb_strlen($url, $this->encoding) > 50) {
            $this->exc("url должен быть короче 50 символов");
        }
        $this->add('url', $url);
        return $this;
    }

    public function exc($text)
    {
        throw new RuntimeException($text);
    }

    /**
     * @throws DOMException
     */
    public function cms($name, $version = FALSE)
    {
        $this->add('platform', $name);
        if ($version !== FALSE) {
            $this->add('version', $version);
        }
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function agency($name)
    {
        $this->add('agency', $name);
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function email($mail)
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $this->exc(' Некорректный Email');
        }
        $this->add('email', $mail);
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function cpa($val = TRUE)
    {
        if (!is_bool($val)) {
            $this->exc(' cpa должен быть boolean');
        }
        $this->add('cpa', ($val) ? '1' : '0');
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function currency($id, $rate, $plus = 0)
    {
        if (strpos($rate, ',') !== FALSE) {
            $this->exc("rate разделяется только точкой");
        }
        if (strpos($plus, ',') !== FALSE) {
            $this->exc("plus разделяется только точкой");
        }

        $c = $this->createElement('currency');

        $c->setAttribute('id', $id);
        $c->setAttribute('rate', $rate);
        if ($plus) {
            $c->setAttribute('plus', $plus);
        }
        if (!$this->currencies) {
            $this->add('currencies');
            $this->currencies = $this->getElementsByTagName('currencies')->item(0);
        }

        $this->currencies->appendChild($c);
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function category($id, $name, $parentId = FALSE)
    {
        if ((!is_int($id)) || ($id < 1)) {
            $this->exc("id должен быть целым положительным числом > 0");
        }

        if (($parentId !== FALSE) && ((!is_int($parentId)) || ($parentId < 1))) {
            $this->exc("parentId должен быть целым положительным числом > 0");
        }

        $c = $this->createElement('category', $name);
        $c->setAttribute('id', $id);
        if ($parentId !== FALSE) {
            $c->setAttribute('parentId', $parentId);
        }
        if (!$this->categories) {
            $this->add('categories');
            $this->categories = $this->getElementsByTagName('categories')->item(0);
        }
        $this->categories->appendChild($c);
        return $this;
    }

    /**
     * @throws DOMException
     */
    public function simple($name, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'simple', $from);
        $offer->addStr('name', $name, 120);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function newOffer($id, $price, $currency, $category, $type, $from)
    {

        if (is_null($this->offer)) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          // если это первый оффер
            $offers = $this->add('offers', ' ');                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      // добавляем элемент offers в DOM
            $begining = $this->saveXML();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             // и пишем поля магазина в новый файл
            $begining = substr($begining, 0, strpos($begining, ' </offers>'));
            if (!is_null($this->bufferSize)) {
                stream_set_write_buffer($this->fp, $this->bufferSize);
            }
            fwrite($this->fp, $begining);
            $this->offer = new YmlOffer($type, $this->encoding);
            $offers->appendChild($this->offer);
        } else {
            fwrite($this->fp, $this->saveXML($this->offer));                // если это не первый оффер, то записываем предыдущий

            while ($this->offer->firstChild) {                            // стираем все его элементы
                $this->offer->removeChild($this->offer->firstChild);
            }

            while ($this->offer->attributes->length) {                        // и стираем все его аттрибуты
                $this->offer->removeAttributeNode($this->offer->attributes->item(0));
            }
        }

        if (preg_match("/[^a-z,A-Z0-9]/", $id)) {
            $this->exc("id должен содержать только латинские буквы и цифры");
        }
        if (strlen($id) > 20) {
            $this->exc("id длиннее 20 символов");
        }

        if ((!is_int($category)) || ($category < 1) || ($category >= pow(10, 19))) {
            $this->exc("categoryId - целое число, не более 18 знаков");
        }

        if ($price) {
            if (!is_int($price) || $price < 0) {
                $this->exc("price должно быть целым и положительным");
            }
        }

        if (!is_null($from)) {
            if (!is_bool($from)) {
                $this->exc('from должен быть boolean');
            }
        }

        $this->offer->setAttribute('id', $id);
        $this->offer->add('currencyId', $currency)
            ->add('categoryId', $category);
        if ($price) {
            $pr = new DomElement('price', $price);
            $this->offer->appendChild($pr);
            if (!is_null($from)) {
                $pr->setAttribute('from', ($from) ? 'true' : 'false');
            }
        }
        return $this->offer;
    }

    /**
     * @throws DOMException
     */
    public function arbitrary($model, $vendor, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'arbitrary', $from);
        $offer->setAttribute('type', 'vendor.model');
        $offer->add('vendor', $vendor);
        $offer->add('model', $model);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function book($name, $publisher, $age, $age_u, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'book', $from);
        $offer->setAttribute('type', 'book');
        $offer->addStr('name', $name, 120);
        $offer->add('publisher', $publisher);
        $offer->age($age, $age_u);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function audiobook($name, $publisher, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'audiobook', $from);
        $offer->setAttribute('type', 'audiobook');
        $offer->addStr('name', $name, 120);
        $offer->add('publisher', $publisher);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function artist($title, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'artist', $from);
        $offer->setAttribute('type', 'artist.title');
        $offer->add('title', $title);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function tour($name, $days, $included, $transport, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'tour', $from);
        $offer->setAttribute('type', 'tour');
        $offer->add('name', $name);

        if (!is_int($days) || $days < 0) {
            $this->exc("days должно быть целым и положительным");
        }
        $offer->add('days', $days);

        $offer->add('included', $included);
        $offer->add('transport', $transport);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function event($name, $place, $date, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'event', $from);
        $offer->setAttribute('type', 'event-ticket');
        $offer->add('name', $name);
        $offer->add('place', $place);
        $offer->add('date', $date);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function medicine($name, $id, $price, $currency, $category, $from = NULL)
    {
        $offer = $this->newOffer($id, $price, $currency, $category, 'medicine', $from);
        $offer->setAttribute('type', 'medicine');
        $offer->add('name', $name);
        $offer->pickup(TRUE);
        $offer->delivery(FALSE);
        return $offer;
    }

    /**
     * @throws DOMException
     */
    public function delivery($cost, $days, $before = -1)
    {
        $dlvs = $this->getElementsByTagName('delivery-options');

        if (!$dlvs->length) {
            $dlv = $this->createElement('delivery-options');
            $this->shop->appendChild($dlv);
        } else {
            $dlv = $dlvs->item(0);
            $opts = $dlv->getElementsByTagName('option');
            if ($opts->length >= 5) {
                $this->exc("максимум 5 опций доставки");
            }
        }

        if (!is_int($cost) || $cost < 0) {
            $this->exc("cost должно быть целым и положительным");
        }
        if (preg_match("/[^0-9\-]/", $days)) {
            $this->exc("days должно состоять из цифр и тирэ");
        }
        if (!is_int($before) || $before > 24) {
            $this->exc("order-before должно быть целым и меньше 25");
        }

        $opt = $this->createElement('option');

        $opt->setAttribute('cost', $cost);
        $opt->setAttribute('days', $days);

        if ($before >= 0) {
            $opt->setAttribute('order-before', $before);
        }

        $dlv->appendChild($opt);

        return $this;
    }

    public function __destruct()
    {
        $this->saveAndClose();
    }

    public function saveAndClose()
    {
        fwrite($this->fp, $this->saveXML($this->offer));                                    // пишем последний оффер и концовку
        fwrite($this->fp, '</offers></shop></yml_catalog>');
        fclose($this->fp);
    }
}

