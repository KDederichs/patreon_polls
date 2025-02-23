import { VoteConfig } from '@/components/polls/tier-vote-config'

export interface PollCreateInput {
  pollName: string
  endsAt: Date
  allowPictures: boolean
  voteConfig: {
    [key: string]: VoteConfig
  }
}
