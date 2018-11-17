<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\StreamSchedule;
use App\Form\EventListener\OnetimeExecutionDateSubscriber;
use App\Form\EventListener\RecurringExecutionDateSubscriber;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UpdateScheduleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class, ['empty_data' => Uuid::uuid4()]);
        $builder->add('wrecked', HiddenType::class, ['empty_data' => false]);

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'stream.form.label.detail.name',
                'required' => true,
                'translation_domain' => 'schedule_create',
                'attr' => ['class' => 'form-control', 'placeholder' => 'name'],
            ]
        );

        $builder->add(
            'command',
            CommandChoiceType::class,
            [
                'label' => 'stream.form.label.detail.command',
                'translation_domain' => 'schedule_create',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]
        );

        $builder->addEventSubscriber(new OnetimeExecutionDateSubscriber());

        $builder->addEventSubscriber(new RecurringExecutionDateSubscriber());

        $builder->add(
            'priority',
            IntegerType::class,
            [
                'label' => 'stream.form.label.detail.priority',
                'translation_domain' => 'schedule_create',
                'empty_data' => 0,
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0, 'max' => 1000],
            ]
        );

        $builder->add(
            'runWithNextExecution',
            CheckboxType::class,
            [
                'label' => 'stream.form.label.detail.run_with_next_execution',
                'required' => false,
                'translation_domain' => 'schedule_create',
            ]
        );

        $builder->add(
            'disabled',
            CheckboxType::class,
            [
                'label' => 'stream.form.label.detail.disabled',
                'translation_domain' => 'schedule_create',
                'required' => false,
            ]
        );

        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'stream.form.label.detail.save',
                'translation_domain' => 'schedule_create',
                'attr' => ['class' => 'btn btn-success btn-lg pull-right'],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => StreamSchedule::class,
            'wrapper_attr' => 'default_wrapper',
        ));
    }
}