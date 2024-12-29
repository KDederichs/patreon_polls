import { VoteConfig } from '@/components/polls/tier-vote-config'

export interface PollCreateInput {
  pollName: string
  endDate: Date
  allowPictures: boolean
  voteConfig: {
    [key: string]: VoteConfig
  }
}
