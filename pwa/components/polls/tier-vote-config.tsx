'use client'
import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'
import { Card, CardBody, CardHeader, Checkbox, Spacer } from '@nextui-org/react'
import { Input } from '@nextui-org/input'
import { useEffect, useState } from 'react'

interface Props {
  selectedTier: PatreonCampaignTier
  previousConfig?: VoteConfig
  onChange: (tierIri: string, config: VoteConfig) => void
}

export interface VoteConfig {
  numberOfOptions?: string
  numberOfVotes?: string
  votingPower: string
  canAddOptions: boolean
  hasLimitedVotes: boolean
}

export default function TierVoteConfig({
  selectedTier,
  onChange,
  previousConfig,
}: Props) {
  const [state, setState] = useState<VoteConfig>(
    previousConfig ?? {
      votingPower: '1',
      canAddOptions: false,
      hasLimitedVotes: false,
      numberOfVotes: '1',
      numberOfOptions: '1',
    },
  )

  console.log(previousConfig)

  useEffect(() => {
    onChange(selectedTier['@id'], state)
  }, [state])

  return (
    <>
      <Spacer y={4} />
      <Card>
        <CardHeader>
          <h3 className="text-medium">{selectedTier.tierName}</h3>
        </CardHeader>
        <CardBody>
          <div className="grid grid-cols-2 gap-5">
            <Checkbox
              isSelected={state.canAddOptions}
              onValueChange={(newValue) =>
                setState({
                  ...state,
                  canAddOptions: newValue,
                })
              }
            >
              Can add options
            </Checkbox>
            {state.canAddOptions ? (
              <Input
                type={'number'}
                label="How many?"
                value={state.numberOfOptions}
                min={'1'}
                onValueChange={(newValue) =>
                  setState({
                    ...state,
                    numberOfOptions: newValue,
                  })
                }
              />
            ) : null}
          </div>
          <div className="mt-2 grid grid-cols-2 gap-5">
            <Checkbox
              isSelected={state.hasLimitedVotes}
              onValueChange={(newValue) =>
                setState({
                  ...state,
                  hasLimitedVotes: newValue,
                })
              }
            >
              Limited votes
            </Checkbox>
            {state.hasLimitedVotes ? (
              <Input
                type={'number'}
                label="How many?"
                value={state.numberOfVotes}
                min={'1'}
                onValueChange={(newValue) =>
                  setState({
                    ...state,
                    numberOfVotes: newValue,
                  })
                }
              />
            ) : null}
          </div>
          <Input
            type={'number'}
            label="Voting power"
            className="mt-2"
            value={state.votingPower}
            min={'1'}
            onValueChange={(newValue) =>
              setState({
                ...state,
                votingPower: newValue,
              })
            }
          />
        </CardBody>
      </Card>
    </>
  )
}
