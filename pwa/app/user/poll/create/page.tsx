'use client'
import React, { useState } from 'react'
import {
  Card,
  CardBody,
  CardHeader,
  Checkbox,
  Divider,
  Spacer,
  cn,
  DatePicker,
  Select,
  SelectItem,
} from '@nextui-org/react'
import { Input } from '@nextui-org/input'
import { getLocalTimeZone, now, ZonedDateTime } from '@internationalized/date'
import { Button } from '@nextui-org/button'
import { useListPatreonUsers } from '@/hooks/query/PatreonUser/useListPatreonUsers'
import { useListPatreonCampaigns } from '@/hooks/query/PatreonCampaign/useListPatreonCampaigns'
import { useListPatreonCampaignTiers } from '@/hooks/query/PatreonCampaignTier/useListPatreonCampaignTiers'
import TierSelector from '@/components/polls/tier-selector'
import { useSyncPatreonCampaigns } from '@/hooks/mutation/PatreonCampaign/useSyncPatreonCampaigns'
import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'
import TierVoteConfig, { VoteConfig } from '@/components/polls/tier-vote-config'
import { useCreatePoll } from '@/hooks/mutation/Poll/useCreatePoll'
import { useRouter } from 'next/navigation'

export default function PollCreatePage() {
  const { data: patreonData } = useListPatreonUsers()
  const patreonSyncMutator = useSyncPatreonCampaigns()

  const isPatreonCreator =
    patreonData?.member?.find((ptrUser) => ptrUser.creator) !== undefined

  const [isPatreonSelected, setIsPatreonSelected] = React.useState(false)
  const [isSubscribeStarSelected, setIsSubscribeStarSelected] =
    React.useState(false)
  const [selectedPatreonCampaign, setSelectedPatreonCampaign] =
    React.useState<string>('')
  const { data: patreonCampaigns, isLoading: patreonCampaignsLoading } =
    useListPatreonCampaigns({ enabled: isPatreonCreator })
  const [selectedTiers, setSelectedTiers] = useState<PatreonCampaignTier[]>([])
  const [voteConfig, setVoteConfig] = useState<{ [key: string]: VoteConfig }>(
    {},
  )
  const [pollName, setPollName] = useState<string>('')
  const [pollEndsDate, setPollEndDate] = useState<ZonedDateTime | null>(
    now(getLocalTimeZone()),
  )
  const [canUploadPictures, setCanUploadPictures] = useState<boolean>(false)
  const router = useRouter()

  const pollCreator = useCreatePoll({
    onSuccess: (poll) => {
      router.push(`/poll/${poll.id}`)
    },
    onError: (error) => {
      console.log(error)
    },
  })

  const onVoteConfigChange = (tierIri: string, config: VoteConfig) => {
    const newVoteConfig = { ...voteConfig }
    newVoteConfig[tierIri] = config
    setVoteConfig(newVoteConfig)
  }

  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex flex-col text-center">
        <h1 className="text-4xl font-medium tracking-tight">
          Create a new Poll
        </h1>
        <Spacer y={4} />
        <h2 className="text-large text-default-500">
          Chose the settings for your poll here.
        </h2>
        <Spacer y={4} />
      </div>
      <h2 className="text-large">Who can vote?</h2>
      <div className="mt-12 grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <Checkbox
              isSelected={isPatreonSelected}
              onValueChange={setIsPatreonSelected}
              disabled={!isPatreonCreator}
            >
              Patreon Subscribers
            </Checkbox>
          </CardHeader>
          {isPatreonSelected ? (
            <>
              <Divider />
              <CardBody>
                <div className="m-2">
                  <h2 className="text-large">
                    For which Campaign are you creating the poll?
                  </h2>
                  <Select
                    items={patreonCampaigns ?? []}
                    label="Patreon campaign"
                    disabled={(patreonCampaigns ?? []).length === 0}
                    selectedKeys={[selectedPatreonCampaign]}
                    placeholder={
                      (patreonCampaigns ?? []).length === 0
                        ? 'It looks like you have no patreon campaigns'
                        : 'Please select your patreon campaign'
                    }
                    isLoading={patreonCampaignsLoading}
                    onChange={(e) => {
                      setSelectedPatreonCampaign(e.target.value)
                    }}
                  >
                    {(campaign) => (
                      <SelectItem key={campaign.id}>
                        {campaign.campaignName}
                      </SelectItem>
                    )}
                  </Select>

                  {(patreonCampaigns ?? []).length === 0 &&
                  !patreonCampaignsLoading ? (
                    <Button
                      className="mb-2 mt-5"
                      variant="ghost"
                      color={'success'}
                      isLoading={patreonSyncMutator.isPending}
                      onPress={() => {
                        patreonSyncMutator.mutate()
                      }}
                    >
                      Sync Patreon
                    </Button>
                  ) : null}

                  <TierSelector
                    campaignId={selectedPatreonCampaign}
                    onTierSelectUpdate={setSelectedTiers}
                  />
                </div>
                <Divider />
                {selectedTiers.map((selectedTier) => {
                  return (
                    <TierVoteConfig
                      key={selectedTier.id}
                      selectedTier={selectedTier}
                      onChange={onVoteConfigChange}
                      previousConfig={voteConfig[selectedTier['@id']]}
                    />
                  )
                })}
              </CardBody>
            </>
          ) : null}
        </Card>
        <Card>
          <CardHeader>
            <Checkbox
              isSelected={isSubscribeStarSelected}
              onValueChange={setIsSubscribeStarSelected}
            >
              SubscribeStar Subscribers
            </Checkbox>
          </CardHeader>
          {isSubscribeStarSelected ? (
            <>
              <Divider />
              <CardBody>
                <h2 className="text-large">Which tiers can vote?</h2>
                <Checkbox
                  aria-label={'Tier 1'}
                  classNames={{
                    base: cn(
                      'inline-flex w-full max-w-md bg-content1',
                      'hover:bg-content2 items-center justify-start',
                      'cursor-pointer rounded-lg gap-2 p-4 border-2 border-transparent',
                      'data-[selected=true]:border-primary',
                      'mt-1 mb-1',
                    ),
                    label: 'w-full',
                  }}
                >
                  <div className="flex w-full justify-between gap-2">
                    Tier 1
                  </div>
                </Checkbox>
                <Checkbox
                  aria-label={'Tier 2'}
                  classNames={{
                    base: cn(
                      'inline-flex w-full max-w-md bg-content1',
                      'hover:bg-content2 items-center justify-start',
                      'cursor-pointer rounded-lg gap-2 p-4 border-2 border-transparent',
                      'data-[selected=true]:border-primary',
                      'mt-1 mb-1',
                    ),
                    label: 'w-full',
                  }}
                >
                  <div className="flex w-full justify-between gap-2">
                    Tier 2
                  </div>
                </Checkbox>
                <Checkbox
                  aria-label={'Tier 3'}
                  classNames={{
                    base: cn(
                      'inline-flex w-full max-w-md bg-content1',
                      'hover:bg-content2 items-center justify-start',
                      'cursor-pointer rounded-lg gap-2 p-4 border-2 border-transparent',
                      'data-[selected=true]:border-primary',
                      'mt-1 mb-1',
                    ),
                    label: 'w-full',
                  }}
                >
                  <div className="flex w-full justify-between gap-2">
                    Tier 3
                  </div>
                </Checkbox>
                <Divider />
                <Spacer y={4} />
                <Card>
                  <CardHeader>
                    <h3 className="text-medium">Tier 1 Settings</h3>
                  </CardHeader>
                  <CardBody>
                    <div className="grid grid-cols-2 gap-5">
                      <Checkbox>Can add options</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <div className="mt-2 grid grid-cols-2 gap-5">
                      <Checkbox>Limited votes</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <Input
                      type={'number'}
                      label="Voting power"
                      className="mt-2"
                    />
                  </CardBody>
                </Card>
                <Spacer y={4} />
                <Card>
                  <CardHeader>
                    <h3 className="text-medium">Tier 2 Settings</h3>
                  </CardHeader>
                  <CardBody>
                    <div className="grid grid-cols-2 gap-5">
                      <Checkbox>Can add options</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <div className="mt-2 grid grid-cols-2 gap-5">
                      <Checkbox>Limited votes</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <Input
                      type={'number'}
                      label="Voting power"
                      className="mt-2"
                    />
                  </CardBody>
                </Card>
                <Spacer y={4} />
                <Card>
                  <CardHeader>
                    <h3 className="text-medium">Tier 3 Settings</h3>
                  </CardHeader>
                  <CardBody>
                    <div className="grid grid-cols-2 gap-5">
                      <Checkbox>Can add options</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <div className="mt-2 grid grid-cols-2 gap-5">
                      <Checkbox>Limited votes</Checkbox>
                      <Input
                        type={'number'}
                        label="How many?"
                      />
                    </div>
                    <Input
                      type={'number'}
                      label="Voting power"
                      className="mt-2"
                    />
                  </CardBody>
                </Card>
              </CardBody>
            </>
          ) : null}
        </Card>
      </div>
      <Spacer y={4} />
      <Card className="w-full">
        <CardHeader>
          <h2 className="text-large">Other settings</h2>
        </CardHeader>
        <CardBody>
          <div className="flex w-full flex-row gap-4">
            <Input
              type={'text'}
              autoComplete="off"
              label="Poll Name"
              value={pollName}
              onValueChange={setPollName}
            />
            <DatePicker
              label="When should this poll end?"
              variant="bordered"
              hideTimeZone
              showMonthAndYearPickers
              value={pollEndsDate}
              isRequired={true}
              onChange={setPollEndDate}
            />
          </div>
          <Spacer y={4} />
          <Checkbox
            checked={canUploadPictures}
            onValueChange={setCanUploadPictures}
          >
            Users can upload pictures for their choices
          </Checkbox>
        </CardBody>
      </Card>
      <Spacer y={5} />
      <Button
        className="w-full"
        variant={'ghost'}
        color={'success'}
        isDisabled={Object.keys(voteConfig).length === 0 || '' === pollName}
        isLoading={pollCreator.isPending}
        onPress={() =>
          pollCreator.mutate({
            pollName,
            endDate: pollEndsDate!.toDate(),
            allowPictures: canUploadPictures,
            voteConfig,
          })
        }
      >
        Create Poll
      </Button>
    </section>
  )
}
