import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface OauthUser extends GenericHydraItem {
  id: string
  username?: string
  creator: boolean
}
