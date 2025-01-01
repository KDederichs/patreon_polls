'use client'
import React, { Usable, useState } from 'react'

import {
  Card,
  CardFooter,
  Spacer,
  Image,
  CardBody,
  Checkbox,
  Skeleton,
} from '@nextui-org/react'
import { Progress } from '@nextui-org/progress'
import clsx from 'clsx'
import { useDropzone } from 'react-dropzone'
import { Input } from '@nextui-org/input'
import { Button } from '@nextui-org/button'
import { Icon } from '@iconify/react'
import { useGetPollInfo } from '@/hooks/query/Poll/useGetPollInfo'
import { useDateFormatter } from '@react-aria/i18n'
import {
  getLocalTimeZone,
  now,
  parseAbsoluteToLocal,
} from '@internationalized/date'
import { useAuthStore } from '@/state/authState'
import { useGetPollOptions } from '@/hooks/query/PollOption/useGetPollOptions'
import { PollOption } from '@/types/entity/PollOption'
import { useUploadImage } from '@/hooks/mutation/PollOption/useUploadImage'
import { useCreatePollOption } from '@/hooks/mutation/PollOption/useCreatePollOption'
import { useQueryClient } from '@tanstack/react-query'
import { GenericHydraItem } from '@/types/GenericHydraItem'
import { toast } from 'react-toastify'
import { useDeleteImage } from '@/hooks/mutation/PollOption/useDeleteImage'

interface PollOptionCardProps {
  pollOption: PollOption
  totalVoteCount: number
}
const PollOptionCard = ({
  pollOption,
  totalVoteCount,
}: PollOptionCardProps) => {
  const [isSelected, setIsSelected] = React.useState(false)
  const isAuthenticated = useAuthStore((state) => state.token !== null)

  return (
    <Card
      radius="lg"
      isFooterBlurred
      className="border-none"
      isPressable
      isDisabled={!isAuthenticated}
      onPress={() => {
        setIsSelected((selected) => !selected)
      }}
    >
      {pollOption.imageUri ? (
        <CardBody className="overflow-visible p-0">
          <Image
            shadow="sm"
            radius="lg"
            width="100%"
            alt={pollOption.optionName}
            className="h-[280px] w-full object-cover"
            src={pollOption.imageUri}
          />
        </CardBody>
      ) : null}

      <CardFooter
        className={clsx(
          'absolute bottom-1 z-10 ml-1 w-[calc(100%_-_8px)] justify-evenly overflow-hidden rounded-large border-1 border-white/20 py-1 shadow-small before:rounded-xl before:bg-white/10',
          isSelected ? 'border-green-500 bg-green-500/50' : '',
        )}
      >
        <Checkbox
          color={'success'}
          isSelected={isSelected}
          onValueChange={setIsSelected}
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
          label={`${pollOption.numberOfVotes}/${totalVoteCount}`}
        />
      </CardFooter>
    </Card>
  )
}

export default function PollVotePage({
  params,
}: {
  params: Usable<{ id: string }>
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
    onError: (error) => toast.error(error.message),
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
      setOptionName('')
      setMediaIri(null)
      setImage(null)
    },
    onError: (error) => {
      toast.error(error.message)
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
    onError: (error) => toast.error(error.message),
  })

  const myOptionsCounter = pollOptions?.reduce((carry, option) => {
    if (option.myOption) {
      return carry + 1
    }
    return carry
  }, 0)

  const maxOptionsReached =
    (myOptionsCounter ?? 0) >= (pollData?.config?.numberOfOptions ?? 1)
  const maxVotesReached = false

  const pollEndTime = pollData?.endsAt
    ? parseAbsoluteToLocal(pollData.endsAt)
    : now(getLocalTimeZone())

  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex flex-col text-center">
        <Skeleton isLoaded={!isPollLoading}>
          <h1 className="text-4xl font-medium tracking-tight">
            {pollData?.pollName}
          </h1>
          <Spacer y={4} />
          <h2 className="text-large text-default-500">
            {formatter.format(pollEndTime.toDate())}
          </h2>
        </Skeleton>
        <Spacer y={4} />
      </div>
      <div className="mt-12 grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        {(pollOptions ?? []).map((pollOption) => (
          <PollOptionCard
            key={pollOption['@id']}
            pollOption={pollOption}
            totalVoteCount={totalVoteCount}
          />
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
    </section>
  )
}
