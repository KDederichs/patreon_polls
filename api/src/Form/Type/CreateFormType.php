<?php

namespace App\Form\Type;

use App\Dto\CreatePollData;
use App\Entity\PatreonCampaign;
use App\Entity\PatreonCampaignTier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class CreateFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder = new DynamicFormBuilder($builder);

        $builder
            ->add('patreonCampaign', EntityType::class, [
                'class' => PatreonCampaign::class,
                'choice_label' => 'campaignName',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('pc')
                        ->where('pc.campaignOwner = :user')
                        ->setParameter('user', $this->security->getUser())
                    ;
                },
                'required' => true,
                'placeholder' => 'Please select a campaign',
            ])
            ->addDependent('votingTiers','patreonCampaign', function (DependentField $field, ?PatreonCampaign $patreonCampaign) {
                //dd($patreonCampaign);
                $field->add(EntityType::class, [
                    'class' => PatreonCampaignTier::class,
                    'choice_label' => 'tierName',
                    'query_builder' => function (EntityRepository $er) use ($patreonCampaign): QueryBuilder {
                        return $er->createQueryBuilder('pct')
                            ->where('pct.campaign = :campaign')
                            ->setParameter('campaign', $patreonCampaign)
                            ;
                    },
                    'disabled' => null === $patreonCampaign,
                    'multiple' => true,
                    'placeholder' => null === $patreonCampaign ? 'Please select a campaign first' : 'Please select the tiers that can vote on this poll.',
                    'required' => $patreonCampaign !== null
                ]);
            })
            ->addDependent('votingPower', 'votingTiers', function (DependentField $field, ?ArrayCollection $votingTiers) {
                if (!$votingTiers || $votingTiers->isEmpty()) {
                    return;
                }
                $field->add(VotingPowerType::class, [
                    'patreon_tiers' => $votingTiers,
                ]);
            })
            ->addDependent('voteLimit', 'votingTiers', function (DependentField $field, ?ArrayCollection $votingTiers) {
                if (!$votingTiers || $votingTiers->isEmpty()) {
                    return;
                }
                $field->add(VotingLimitType::class, [
                    'patreon_tiers' => $votingTiers,
                ]);
            })
            ->addDependent('maxOptionAdd', 'votingTiers', function (DependentField $field, ?ArrayCollection $votingTiers) {
                if (!$votingTiers || $votingTiers->isEmpty()) {
                    return;
                }
                $field->add(AddMaxOptionType::class, [
                    'patreon_tiers' => $votingTiers,
                ]);
            })
            ->add('pollName', TextType::class, [
                'label' => 'Poll Name',
                'required' => true
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'End Date',
                'required' => false
            ])
            ->add('create', SubmitType::class, [
                'label' => 'Create Poll',
                'attr' => [
                    'class' => 'rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreatePollData::class,
        ]);
    }


}
