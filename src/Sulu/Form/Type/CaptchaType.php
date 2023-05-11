<?php

declare(strict_types=1);

namespace App\Sulu\Form\Type;

use Gregwar\CaptchaBundle\Type\CaptchaType as TypeCaptchaType;
use Sulu\Bundle\FormBundle\Dynamic\FormFieldTypeConfiguration;
use Sulu\Bundle\FormBundle\Dynamic\FormFieldTypeInterface;
use Sulu\Bundle\FormBundle\Dynamic\Types\SimpleTypeTrait;
use Sulu\Bundle\FormBundle\Entity\FormField;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormBuilderInterface;

#[AutoconfigureTag('sulu_form.dynamic.type', ['alias' => 'captcha'])]
class CaptchaType implements FormFieldTypeInterface
{
    use SimpleTypeTrait;

    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public function getConfiguration(): FormFieldTypeConfiguration
    {
        return new FormFieldTypeConfiguration(
            'app.admin.form.type.captcha',
            $this->projectDir . '/config/types/field_captcha.xml',
        );
    }

    public function build(FormBuilderInterface $builder, FormField $field, string $locale, array $options): void
    {
        $builder->add($field->getKey(), TypeCaptchaType::class, $options);
    }
}
