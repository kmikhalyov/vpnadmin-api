<?php

namespace VPNAdmin\ApiBundle\Serializer;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use JMS\Serializer\Handler\FormErrorHandler as JMSFormErrorHandler;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\YamlSerializationVisitor;

class FormErrorHandler extends JMSFormErrorHandler
{
    private function convertFormToArray(GenericSerializationVisitor $visitor, Form $data)
    {
        $isRoot = null === $visitor->getRoot();

        $form = new \ArrayObject();
        $errors = array();
        foreach ($data->getErrors() as $error) {
//            $errors[] = $this->getErrorMessage($error);
            $errors[] = $error->getMessage();
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = array();
        foreach ($data->all() as $child) {
            if ($child instanceof Form) {
                $form[$child->getName()] = $this->convertFormToArray($visitor, $child);
            }
        }

        if ($isRoot) {
            $visitor->setRoot($form);
        }

        return $form;
    }

    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    public function serializeFormToYml(YamlSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }
}
