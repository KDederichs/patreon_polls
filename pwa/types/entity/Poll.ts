import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface Poll extends GenericHydraItem {
  id: string
  createdAt: string
  pollName: string
  endsAt?: string
}
