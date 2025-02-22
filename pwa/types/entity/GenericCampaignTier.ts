import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface GenericCampaignTier extends GenericHydraItem {
  id: string
  createdAt: string
  tierName: string
}
