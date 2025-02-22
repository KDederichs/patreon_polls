'use client'

import PollList from '@/components/polls/poll-list'
import { Spacer } from "@heroui/react"
import { Button } from "@heroui/button"
import { Icon } from '@iconify/react'
import { Link } from "@heroui/link"

export default function PollsPage() {
  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex max-w-xl flex-col text-center">
        <h1 className="text-4xl font-medium tracking-tight">Your polls</h1>
        <Spacer y={4} />
        <h2 className="text-large text-default-500">
          Here you can find a list of polls you have created in the past.
        </h2>
        <Spacer y={4} />
        <div className="flex w-full justify-center gap-2">
          <Button
            as={Link}
            variant="ghost"
            color={'success'}
            startContent={<Icon icon={'ic:round-post-add'} />}
            href={'/user/poll/create'}
          >
            New Poll
          </Button>
        </div>
      </div>
      <div className="mt-12 w-full">
        <PollList />
      </div>
    </section>
  )
}
