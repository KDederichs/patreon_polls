import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface PatreonCampaign extends GenericHydraItem {
  id: string
  createdAt: string
  campaignName: string
}
