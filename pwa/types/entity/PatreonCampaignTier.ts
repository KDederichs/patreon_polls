import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface PatreonCampaignTier extends GenericHydraItem {
  id: string
  createdAt: string
  tierName: string
}
