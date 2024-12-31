import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface PollOption extends GenericHydraItem {
  id: string
  createdAt: string
  optionName: string
  numberOfVotes: number
  imageUri?: string
  myOption: boolean
}
