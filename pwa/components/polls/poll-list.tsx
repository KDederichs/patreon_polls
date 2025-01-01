'use client'

import { ListPoll } from '@/types/ListPoll'
import { Card, CardBody, Chip } from '@nextui-org/react'
import { Button } from '@nextui-org/button'
import moment from 'moment-timezone'
import { useListPolls } from '@/hooks/query/Poll/useListPolls'
import {
  getLocalTimeZone,
  now,
  parseAbsoluteToLocal,
} from '@internationalized/date'
import { useDateFormatter } from '@react-aria/i18n'
import { useRouter } from 'next/navigation'

export default function PollList() {
  const { data, isLoading } = useListPolls()
  let formatter = useDateFormatter({ dateStyle: 'full', timeStyle: 'long' })
  let formatterShort = useDateFormatter({ dateStyle: 'short' })
  const router = useRouter()

  return (
    <div>
      {data?.map((poll) => {
        const pollEndTime = poll?.endsAt
          ? parseAbsoluteToLocal(poll.endsAt)
          : now(getLocalTimeZone())

        const closed = pollEndTime.toDate() < new Date()

        return (
          <Card
            className="m-2"
            key={poll['@id']}
          >
            <CardBody>
              <div className="flex flex-row justify-between">
                <div>
                  <p>{poll.pollName}</p>
                  <p className="text-xs text-gray-400">{`created at ${formatterShort.format(parseAbsoluteToLocal(poll.createdAt).toDate())}`}</p>
                </div>
                <p className="flex items-center justify-center text-center">
                  <Chip
                    color={closed ? 'danger' : 'success'}
                    variant="flat"
                  >
                    {closed
                      ? 'Closed'
                      : `Open till: ${formatter.format(pollEndTime.toDate())}`}
                  </Chip>
                </p>
                <Button
                  color={closed ? 'primary' : 'success'}
                  radius="full"
                  variant={closed ? 'bordered' : 'ghost'}
                  onPress={() => {
                    router.push(`/poll/${poll.id}`)
                  }}
                >
                  {closed ? 'View results' : 'Vote'}
                </Button>
              </div>
            </CardBody>
          </Card>
        )
      })}
    </div>
  )
}
