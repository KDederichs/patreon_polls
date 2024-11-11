"use client"

import {ListPoll} from "@/types/ListPoll";
import PollList from "@/components/polls/poll-list";
import {Spacer} from "@nextui-org/react";
import {Button} from "@nextui-org/button";
import {Icon} from "@iconify/react";
import {Link} from "@nextui-org/link";

const dummyPolls: ListPoll[] = [
  {
    pollName: 'Test Poll',
    creatorName: 'Ladon',
    closed: false,
    openTill: '2024-10-20T23:50:21.817Z',
    pollId: '1'
  },
  {
    pollName: 'Test Poll 2',
    creatorName: 'Ladon',
    closed: true,
    openTill: '20.10.2024 10:00:00',
    pollId: '2'
  }
]

export default function PollsPage() {
  return (
    <section className="flex flex-col items-center py-24">
      <div className="flex max-w-xl flex-col text-center">
        <h1 className="text-4xl font-medium tracking-tight">Your polls</h1>
        <Spacer y={4}/>
        <h2 className="text-large text-default-500">
          Here you can find a list of currently active polls as well as polls you have previously voted on.
        </h2>
        <Spacer y={4}/>
        <div className="flex w-full justify-center gap-2">
          <Button
            as={Link}
            variant="ghost"
            color={"success"}
            startContent={<Icon icon={'ic:round-post-add'}/>}
            href={'/user/poll/create'}
          >
            New Poll
          </Button>
        </div>
      </div>
      <div className="mt-12 w-full">
        <PollList polls={dummyPolls}/>
      </div>
    </section>
  )
}
