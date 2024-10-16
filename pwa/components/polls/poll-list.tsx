"use client"

import {ListPoll} from "@/types/ListPoll";
import {Card, CardBody, Chip} from "@nextui-org/react";
import {Button} from "@nextui-org/button";
import moment from "moment-timezone";

interface Props {
  polls: ListPoll[]
}

export default function PollList({polls}: Props) {
  return (
    <div>
      {polls.map((poll) =>
        <Card className="m-2" key={poll.pollId}>
          <CardBody>
            <div className="flex flex-row justify-between">
              <div>
                <p>{poll.pollName}</p>
                <p className="text-xs text-gray-400">{`by ${poll.creatorName}`}</p>
              </div>
              <p className="flex text-center items-center justify-center">
                <Chip
                  color={poll.closed ? 'danger' : 'success'}
                  variant="flat">
                  {poll.closed ? 'Closed' : `Open till: ${moment(poll.openTill).format('lll')}`}
                </Chip>
              </p>
              <Button
                color={poll.closed ? 'primary' : 'success'}
                radius="full"
                variant={poll.closed ? 'bordered' : 'ghost'}
              >
                {poll.closed ? 'View results' : 'Vote'}
              </Button>
            </div>
          </CardBody>
        </Card>
      )}
    </div>
  )
}
