'use client'
import React from 'react'

import {
  Card,
  CardFooter,
  Spacer,
  Image,
  CardBody,
  Checkbox,
} from '@nextui-org/react'
import { Progress } from '@nextui-org/progress'
import clsx from 'clsx'
import { useDropzone } from 'react-dropzone'
import { Input } from '@nextui-org/input'
import { Button } from '@nextui-org/button'
import { Icon } from '@iconify/react'

const PollOptionCard = () => {
  const [isSelected, setIsSelected] = React.useState(false)

  return (
    <Card
      radius="lg"
      isFooterBlurred
      className="border-none"
      isPressable
      onPress={() => {
        setIsSelected((selected) => !selected)
      }}
    >
      <CardBody className="overflow-visible p-0">
        <Image
          shadow="sm"
          radius="lg"
          width="100%"
          alt="Woman listing to music"
          className="h-[280px] w-full object-cover"
          src="https://nextui.org/images/hero-card.jpeg"
        />
      </CardBody>
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
          Character Name Here
        </Checkbox>
        <Progress
          className="max-w-[50%] shadow-lg"
          size="sm"
          aria-label="Loading..."
          value={30}
          color={'success'}
          label={'1/100'}
        />
      </CardFooter>
    </Card>
  )
}

export default function PollVotePage() {
  const { acceptedFiles, getRootProps, getInputProps } = useDropzone()
  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex flex-col text-center">
        <h1 className="text-4xl font-medium tracking-tight">POLL NAME</h1>
        <Spacer y={4} />
        <h2 className="text-large text-default-500">
          Open till: 01.01.2001 - 00:00
        </h2>
        <Spacer y={4} />
      </div>
      <div className="mt-12 grid w-full grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        <PollOptionCard />
        <PollOptionCard />
        <PollOptionCard />
        <PollOptionCard />
        <PollOptionCard />
        <PollOptionCard />
        <Card
          radius="lg"
          className="h-[280px] border-none"
        >
          <CardBody className="overflow-visible">
            <div
              {...getRootProps()}
              className="flex-column flex h-full items-center justify-center rounded border-2 border-dotted"
            >
              <input {...getInputProps()} />
              <div className="grid-cols- grid">
                <div className="flex w-full justify-center p-2">
                  <Icon
                    icon={'mdi-light:image'}
                    style={{ fontSize: '36px' }}
                  />
                </div>
                <p className="text-xs">Optional character image (no NSFW)</p>
              </div>
            </div>
          </CardBody>
          <CardFooter>
            <div className="grid w-full grid-cols-1 gap-5">
              <Input
                type={'text'}
                autoComplete={'off'}
                autoCorrect={'off'}
                label={'Character name'}
              />
              <Button
                color={'success'}
                variant={'solid'}
              >
                Add
              </Button>
            </div>
          </CardFooter>
        </Card>
      </div>
    </section>
  )
}
