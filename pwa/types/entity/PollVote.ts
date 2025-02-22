import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface PollVote extends GenericHydraItem {
  id: string
  createdAt: string
  votePower: number
  pollOption: string
}
