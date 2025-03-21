'use client'
import React, { useEffect, useState } from 'react'

import {
  Card,
  CardFooter,
  Spacer,
  Image,
  CardBody,
  Checkbox,
  Skeleton,
} from '@heroui/react'
import { Progress } from '@heroui/progress'
import clsx from 'clsx'
import { useDropzone } from 'react-dropzone'
import { Input } from '@heroui/input'
import { Button } from '@heroui/button'
import { Icon } from '@iconify/react'
import { useGetPollInfo } from '@/hooks/query/Poll/useGetPollInfo'
import { useDateFormatter } from '@react-aria/i18n'
import { parseAbsoluteToLocal } from '@internationalized/date'
import { useAuthStore } from '@/state/authState'
import { useGetPollOptions } from '@/hooks/query/PollOption/useGetPollOptions'
import { PollOption } from '@/types/entity/PollOption'
import { useUploadImage } from '@/hooks/mutation/PollOption/useUploadImage'
import { useCreatePollOption } from '@/hooks/mutation/PollOption/useCreatePollOption'
import { useQueryClient } from '@tanstack/react-query'
import { GenericHydraItem } from '@/types/GenericHydraItem'
import { useDeleteImage } from '@/hooks/mutation/PollOption/useDeleteImage'
import { useGetMyVotes } from '@/hooks/query/PollVote/useGetMyVotes'
import { PollVote } from '@/types/entity/PollVote'
import { useCreatePollVote } from '@/hooks/mutation/PollVote/useCreatePollVote'
import { useDeletePollVote } from '@/hooks/mutation/PollVote/useDeletePollVote'
import { motion } from 'framer-motion'
import { Link } from '@heroui/link'
import { showApiError } from '@/util'

interface PollOptionCardProps {
  pollOption: PollOption
  totalVoteCount: number
  votes: PollVote[]
  pollId: string
  disabled: boolean
}

const spring = {
  type: 'spring',
  damping: 25,
  stiffness: 120,
}

const PollOptionCard = ({
  pollOption,
  totalVoteCount,
  votes,
  pollId,
  disabled,
}: PollOptionCardProps) => {
  const isAuthenticated = useAuthStore((state) => state.token !== null)
  const [vote, setVote] = useState<PollVote | null>(null)
  const queryClient = useQueryClient()

  useEffect(() => {
    const v = votes.find((v1) => {
      return v1.pollOption === pollOption['@id']
    })
    setVote(v ?? null)
  }, [votes, pollOption])

  const isSelected = vote !== null

  const pollVoter = useCreatePollVote({
    onSuccess: (pollVote) => {
      queryClient.setQueryData(
        ['list', `/api/polls/${pollId}/my-votes`],
        (oldData: Array<GenericHydraItem> | undefined) => {
          if (undefined === oldData) {
            return [pollOption]
          }
          return [...oldData, pollVote]
        },
      )
      queryClient.setQueryData([pollOption['@id']], pollVote)
      queryClient.refetchQueries({
        queryKey: ['list', `/api/polls/${pollId}/options`],
      })
    },
    onError: (error) => showApiError(error),
  })

  const pollVoteDeleter = useDeletePollVote({
    onSuccess: () => {
      const iri = vote!['@id']
      queryClient.setQueryData(
        ['list', `/api/polls/${pollId}/my-votes`],
        (oldData: Array<GenericHydraItem> | undefined) => {
          if (undefined === oldData) {
            return []
          }
          return [...oldData.filter((old) => old['@id'] !== iri)]
        },
      )
      queryClient.setQueryData([iri], undefined)
      queryClient.refetchQueries({
        queryKey: ['list', `/api/polls/${pollId}/options`],
      })
    },
    onError: (error) => showApiError(error),
  })

  const isDisabled =
    !isAuthenticated ||
    pollVoter.isPending ||
    pollVoteDeleter.isPending ||
    disabled

  const onPress = () => {
    if (isDisabled) {
      return
    }

    if (isSelected) {
      if (!pollVoteDeleter.isPending) {
        pollVoteDeleter.mutate(vote['@id'])
      }
    } else {
      if (!pollVoter.isPending) {
        pollVoter.mutate({
          pollOption: pollOption['@id'],
          poll: `/api/polls/${pollId}`,
        })
      }
    }
  }

  let imageCardWidth = 380
  let imageCardHeight = 480

  if (pollOption.imageOrientation === 'landscape') {
    imageCardWidth = 480
    imageCardHeight = 380
  }

  if (pollOption.imageOrientation === 'square') {
    imageCardWidth = 480
    imageCardHeight = 480
  }

  return (
    <Card
      radius="lg"
      isFooterBlurred
      className="border-none"
      isPressable={!isDisabled}
      isDisabled={isDisabled}
      onPress={onPress}
    >
      {pollOption.imageUri ? (
        <CardBody className="overflow-visible p-0">
          <Image
            shadow="sm"
            radius="lg"
            width="100%"
            alt={pollOption.optionName}
            className={`h-[${imageCardHeight}px] w-full min-w-[${imageCardWidth}px] object-cover`}
            src={pollOption.imageUri}
          />
        </CardBody>
      ) : (
        <CardBody className="overflow-visible p-0">
          <div
            className={
              'flex h-[480px] min-w-[380px] items-center justify-center'
            }
          >
            <h1>{pollOption.optionName} (No picture)</h1>
          </div>
        </CardBody>
      )}

      <CardFooter
        className={clsx(
          'absolute bottom-1 z-10 ml-1 w-[calc(100%_-_8px)] justify-evenly overflow-hidden rounded-large border-1 border-white/20 bg-black/50 py-1 shadow-small before:rounded-xl before:bg-white/10',
          isSelected ? 'border-green-500 bg-green-600/80' : '',
        )}
      >
        <Checkbox
          color={'success'}
          isSelected={isSelected}
          onValueChange={onPress}
          isDisabled={isDisabled}
          classNames={{
            label: 'text-[#ffffff]',
          }}
          size="sm"
        >
          {pollOption.optionName}
        </Checkbox>
        <Progress
          className="ml-3 max-w-[50%] shadow-lg"
          size="sm"
          aria-label={`${pollOption.numberOfVotes}/${totalVoteCount} Votes`}
          value={pollOption.numberOfVotes}
          maxValue={totalVoteCount}
          color={'success'}
          classNames={{
            label: 'text-[#ffffff]',
            track: 'bg-gray-700',
          }}
          label={`${pollOption.numberOfVotes} out of ${totalVoteCount} votes`}
        />
      </CardFooter>
    </Card>
  )
}

export default function PollVotePage({
  params,
}: {
  params: Promise<{ id: string }>
}) {
  const [image, setImage] = useState<File | null>(null)

  const { getRootProps, getInputProps } = useDropzone({
    multiple: false,
    accept: { 'image/*': [] },
    maxFiles: 1,
    onDrop: (acceptedFiles) => {
      const droppedImage = acceptedFiles.pop()
      if (droppedImage) {
        setImage(droppedImage)
      }
    },
  })
  const paramsResolved = React.use(params)
  const { data: pollData, isLoading: isPollLoading } = useGetPollInfo({
    pollId: paramsResolved.id,
  })
  const { data: pollOptions, isLoading: pollOptionsLoading } =
    useGetPollOptions({
      pollId: paramsResolved.id,
    })

  const { data: myVotes, isLoading: myVotesAreLoading } = useGetMyVotes({
    pollId: paramsResolved.id,
  })
  let formatter = useDateFormatter({ dateStyle: 'full', timeStyle: 'long' })
  const isAuthenticated = useAuthStore((state) => state.token !== null)

  const totalVoteCount = (pollOptions ?? []).reduce((carry, currentValue) => {
    return carry + currentValue.numberOfVotes
  }, 0)
  const [optionName, setOptionName] = useState<string>('')
  const [mediaIri, setMediaIri] = useState<string | null>(null)

  const queryClient = useQueryClient()
  const imageDeleter = useDeleteImage({
    onSuccess: () => {
      setMediaIri(null)
    },
    onError: (error) => showApiError(error),
  })

  const optionCreator = useCreatePollOption({
    onSuccess: (pollOption) => {
      queryClient.setQueryData(
        ['list', `/api/polls/${paramsResolved.id}/options`],
        (oldData: Array<GenericHydraItem> | undefined) => {
          if (undefined === oldData) {
            return [pollOption]
          }
          return [...oldData, pollOption]
        },
      )
      queryClient.setQueryData([pollOption['@id']], pollOption)
      queryClient.refetchQueries({
        queryKey: ['list', `/api/polls/${paramsResolved.id}/my-votes`],
      })
      setOptionName('')
      setMediaIri(null)
      setImage(null)
    },
    onError: (error) => {
      showApiError(error)
      if (mediaIri) {
        imageDeleter.mutate(mediaIri)
      }
      setMediaIri(null)
      setImage(null)
    },
  })
  const imageUploader = useUploadImage({
    onSuccess: (media) => {
      setMediaIri(media['@id'])
      optionCreator.mutate({
        optionName,
        poll: `/api/polls/${paramsResolved.id}`,
        image: media['@id'],
      })
    },
    onError: (error) => showApiError(error),
  })

  const myOptionsCounter = pollOptions?.reduce((carry, option) => {
    if (option.myOption) {
      return carry + 1
    }
    return carry
  }, 0)

  const maxOptionsReached =
    (myOptionsCounter ?? 0) >= (pollData?.config?.numberOfOptions ?? 1)
  const maxVotesReached =
    (myVotes?.length ?? 0) >= (pollData?.config?.numberOfVotes ?? 1) &&
    (pollData?.config?.hasLimitedVotes ?? false)
  const pollEndTime = pollData?.endsAt
    ? parseAbsoluteToLocal(pollData.endsAt)
    : null

  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex flex-col text-center">
        <Skeleton isLoaded={!isPollLoading}>
          <h1 className="text-4xl font-medium tracking-tight">
            {pollData?.pollName}
          </h1>
          {pollEndTime !== null && (
            <>
              <Spacer y={4} />
              <h2 className="text-large text-default-500">
                Ends at: {formatter.format(pollEndTime.toDate())}
              </h2>
            </>
          )}
        </Skeleton>
        <Spacer y={4} />
      </div>
      <div className="mt-12 grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        {(pollOptions ?? [])
          .sort((o1, o2) => o2.numberOfVotes - o1.numberOfVotes)
          .map((pollOption) => (
            <motion.div
              key={pollOption['@id']}
              animate={spring}
              layout
            >
              <PollOptionCard
                pollOption={pollOption}
                totalVoteCount={totalVoteCount}
                votes={myVotes ?? []}
                pollId={paramsResolved.id}
                disabled={maxVotesReached}
              />
            </motion.div>
          ))}
        {pollData?.config?.canAddOptions && !maxOptionsReached ? (
          <Card
            radius="lg"
            isFooterBlurred={image !== undefined}
            className="h-[280px] border-none"
          >
            <Skeleton isLoaded={!isPollLoading}>
              {pollData?.allowPictures ? (
                <CardBody
                  className={clsx('overflow-visible', image ? 'p-0' : '')}
                >
                  {!image ? (
                    <div
                      {...getRootProps()}
                      className="flex-column flex h-full items-center justify-center rounded border-2 border-dotted"
                    >
                      <input
                        {...getInputProps()}
                        disabled={!isAuthenticated}
                      />
                      <div className="grid-cols- grid">
                        <div className="flex w-full justify-center p-2">
                          <Icon
                            icon={'mdi-light:image'}
                            style={{ fontSize: '36px' }}
                          />
                        </div>
                        <p className="text-xs">
                          Optional character image (no NSFW)
                        </p>
                      </div>
                    </div>
                  ) : (
                    <div>
                      <Button
                        className={'absolute right-1 top-1 z-40 rounded-xl'}
                        color={'danger'}
                        variant={'ghost'}
                        onPress={() => {
                          setImage(null)
                        }}
                      >
                        X
                      </Button>
                      <Image
                        shadow="sm"
                        radius="lg"
                        width="100%"
                        alt={optionName}
                        className="h-[280px] w-full object-cover"
                        src={URL.createObjectURL(image)}
                      />
                    </div>
                  )}
                </CardBody>
              ) : null}
              <CardFooter
                className={
                  image
                    ? 'absolute bottom-1 z-10 ml-1 w-[calc(100%_-_8px)] justify-evenly overflow-hidden rounded-large border-1 border-white/20 py-1 shadow-small before:rounded-xl before:bg-white/10'
                    : undefined
                }
              >
                <div className="grid w-full grid-cols-1 gap-5">
                  <Input
                    type={'text'}
                    autoComplete={'off'}
                    autoCorrect={'off'}
                    label={'Character name'}
                    required={true}
                    value={optionName}
                    onValueChange={setOptionName}
                  />
                  <Button
                    color={'success'}
                    variant={'solid'}
                    isLoading={
                      optionCreator.isPending ||
                      imageUploader.isPending ||
                      imageDeleter.isPending
                    }
                    isDisabled={!isAuthenticated || optionName.trim() === ''}
                    onPress={() => {
                      if (pollData?.allowPictures) {
                        const pictureFormData = new FormData()
                        if (image) {
                          pictureFormData.append('file', image)
                          imageUploader.mutate(pictureFormData)
                          return
                        }
                      }

                      optionCreator.mutate({
                        optionName,
                        poll: `/api/polls/${paramsResolved.id}`,
                      })
                    }}
                  >
                    Add
                  </Button>
                </div>
              </CardFooter>
            </Skeleton>
          </Card>
        ) : null}
      </div>
      {pollData?.myPoll && (
        <>
          <Spacer y={4} />
          <Button
            as={Link}
            href={`/poll/${pollData.id}/download-marbles`}
            target="_blank"
            variant="ghost"
            color={'secondary'}
          >
            Download Marbles
          </Button>
        </>
      )}
    </section>
  )
}
