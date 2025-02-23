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
} from '@heroui/react'
import { Input } from '@heroui/input'
import { getLocalTimeZone, now, ZonedDateTime } from '@internationalized/date'
import { Button } from '@heroui/button'
import { useListPatreonUsers } from '@/hooks/query/PatreonUser/useListPatreonUsers'
import { useListPatreonCampaigns } from '@/hooks/query/PatreonCampaign/useListPatreonCampaigns'
import { useListPatreonCampaignTiers } from '@/hooks/query/PatreonCampaignTier/useListPatreonCampaignTiers'
import TierSelector from '@/components/polls/tier-selector'
import { useSyncPatreonCampaigns } from '@/hooks/mutation/PatreonCampaign/useSyncPatreonCampaigns'
import { GenericCampaignTier } from '@/types/entity/GenericCampaignTier'
import TierVoteConfig, { VoteConfig } from '@/components/polls/tier-vote-config'
import { useCreatePoll } from '@/hooks/mutation/Poll/useCreatePoll'
import { useRouter } from 'next/navigation'
import { useListSubscribestarUser } from '@/hooks/query/SubscribestarUser/useListSubscribestarUser'
import { useListSubscribestarTiers } from '@/hooks/query/SubscribestarTier/useListSubscribestarTiers'
import { useSyncSubscribestarTiers } from '@/hooks/mutation/SubscribestarTier/useSyncSubscribestarTiers'

export default function PollCreatePage() {
  const { data: patreonData } = useListPatreonUsers()
  const { data: subscribestarUsers } = useListSubscribestarUser()
  const patreonSyncMutator = useSyncPatreonCampaigns()

  const isPatreonCreator =
    patreonData?.member?.find((ptrUser) => ptrUser.creator) !== undefined

  const subscribestarCreatorUser = subscribestarUsers?.member?.find(
    (ptrUser) => ptrUser.creator,
  )

  const subscribestarSyncMutator = useSyncSubscribestarTiers({
    subscribestarUserId: subscribestarCreatorUser?.id ?? '',
  })

  const isSubscribestarCreator = subscribestarCreatorUser !== undefined

  const [isPatreonSelected, setIsPatreonSelected] = React.useState(false)
  const [isSubscribeStarSelected, setIsSubscribeStarSelected] =
    React.useState(false)
  const [selectedPatreonCampaign, setSelectedPatreonCampaign] =
    React.useState<string>('')
  const { data: patreonCampaigns, isLoading: patreonCampaignsLoading } =
    useListPatreonCampaigns({ enabled: isPatreonCreator })
  const [selectedTiersPatreon, setSelectedTiersPatreon] = useState<
    GenericCampaignTier[]
  >([])
  const [selectedTiersSubscribestar, setSelectedTiersSubscribestar] = useState<
    GenericCampaignTier[]
  >([])
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
                    id={selectedPatreonCampaign}
                    onTierSelectUpdate={setSelectedTiersPatreon}
                    tierLoader={useListPatreonCampaignTiers}
                  />
                </div>
                <Divider />
                {selectedTiersPatreon.map((selectedTier) => {
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
              disabled={!isSubscribestarCreator}
            >
              SubscribeStar Subscribers
            </Checkbox>
          </CardHeader>
          {isSubscribeStarSelected ? (
            <>
              <Divider />
              <CardBody>
                <Button
                  className="mb-2 mt-5"
                  variant="ghost"
                  color={'success'}
                  isLoading={subscribestarSyncMutator.isPending}
                  onPress={() => {
                    subscribestarSyncMutator.mutate()
                  }}
                >
                  Sync Subscribestar
                </Button>
                <TierSelector
                  id={subscribestarCreatorUser!.id}
                  onTierSelectUpdate={setSelectedTiersSubscribestar}
                  tierLoader={useListSubscribestarTiers}
                />
                <Divider />
                {selectedTiersSubscribestar.map((selectedTier) => {
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
            endsAt: pollEndsDate!.toDate(),
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
