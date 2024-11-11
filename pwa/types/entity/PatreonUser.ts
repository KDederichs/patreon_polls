import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface PatreonUser extends GenericHydraItem {
  id: string
  username?: string
  creator: boolean
}
