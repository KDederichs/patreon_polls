import { GenericHydraItem } from '@/types/GenericHydraItem'

export interface Poll extends GenericHydraItem {
  id: string
  createdAt: string
  pollName: string
  endsAt?: string
  allowPictures: boolean
  myPoll: boolean
  config?: {
    numberOfOptions?: number
    numberOfVotes?: number
    votingPower: number
    canAddOptions: boolean
    hasLimitedVotes: boolean
  }
}
