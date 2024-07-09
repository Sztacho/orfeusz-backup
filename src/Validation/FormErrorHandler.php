<?php

namespace App\Validation;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class FormErrorHandler
{
    public function __construct(
        private TranslatorInterface  $translator,
        private FormFactoryInterface $formFactory
    ) {
    }

    public function handle(string $formType, Request $request, mixed $object = null): ?array
    {
        $form = $this->formFactory->create($formType, $object);

        return $this->handleForm($form, $request, $object);
    }

    public function handleForm(FormInterface $form, Request $request, mixed $object = null): ?array
    {
        $form->handleRequest($request);

        if ( ! $form->isSubmitted()) {
            $form->submit([]);
        }

        if ( ! $form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $field = $this->getFieldPath($error->getOrigin());
                $message = $this->translator->trans($error->getMessage());
                if ( ! empty($field)) {
                    $errorList['fields'][$field] = $message;
                } else {
                    $errorList['general'] = $message;
                }
            }
        }

        return $errorList ?? null;
    }

    private function getFieldPath(FormInterface $form): string
    {
        $fieldsName = [$form->getName()];
        $parentForm = $form->getParent();

        while ($parentForm !== null) {
            $fieldsName[] = $parentForm->getName();
            $parentForm = $parentForm->getParent();
        }

        $fieldsName = array_reverse($fieldsName);

        $fieldPath = '';
        foreach ($fieldsName as $key => $field) {
            if ($key > 1) {
                $fieldPath .= '[' . $field . ']';
                break;
            }

            $fieldPath = $field;
        }

        return $fieldPath;
    }
}