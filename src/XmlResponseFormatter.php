<?php

namespace Qualp\Yii2XMLParser;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMText;
use Qualp\Yii2XMLParser\Helpers\StringHelper;
use Traversable;

class XmlResponseFormatter
{
    public function format($data)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = new DOMElement('response');
        $dom->appendChild($root);
        $this->buildXml($root, $data);

        $xml = $dom->saveXML();

        return $xml;
    }

    protected function buildXml($element, $data)
    {
        if (is_array($data) || ($data instanceof Traversable)) {
            foreach ($data as $name => $value) {
                if (is_int($name) && is_object($value)) {
                    $this->buildXml($element, $value);
                } elseif (is_array($value) || is_object($value)) {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $this->buildXml($child, $value);
                } else {
                    $child = new DOMElement($this->getValidXmlElementName($name));
                    $element->appendChild($child);
                    $child->appendChild(new DOMText($this->formatScalarValue($value)));
                }
            }
        } else {
            $element->appendChild(new DOMText($this->formatScalarValue($data)));
        }
    }

    protected function getValidXmlElementName($name)
    {
        if (empty($name) || is_int($name) || !$this->isValidXmlName($name)) {
            return 'item';
        }

        return $name;
    }

    protected function isValidXmlName($name)
    {
        try {
            new DOMElement($name);

            return true;
        } catch (DOMException $e) {
            return false;
        }
    }

    protected function formatScalarValue($value)
    {
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        if (is_float($value)) {
            return StringHelper::floatToString($value);
        }

        return (string) $value;
    }
}
