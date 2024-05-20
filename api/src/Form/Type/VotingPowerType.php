<?php

namespace App\Form\Type;

use App\Entity\PatreonCampaignTier;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VotingPowerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['patreon_tiers'] ?? [] as $tier) {
            assert($tier instanceof PatreonCampaignTier);
            $builder
                ->add($tier->getId(), IntegerType::class, [
                    'label' => 'Voting Power: '.$tier->getTierName(),
                    'required' => true,
                    'empty_data' => 1
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'patreon_tiers' => null,
            ])
        ;
    }
}
