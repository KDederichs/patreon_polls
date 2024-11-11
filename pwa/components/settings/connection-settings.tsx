'use client'

import type {CardProps} from "@nextui-org/react";

import React from "react";
import {Card, CardHeader, CardBody, Button} from "@nextui-org/react";
import {Icon} from "@iconify/react";
import CellWrapper from "@/components/common/cell-wrapper";
import { Link } from '@nextui-org/link'
import { toast } from 'react-toastify'
import { useListPatreonUsers } from '@/hooks/query/PatreonUser/useListPatreonUsers'


export default function ConnectionSettings(props: CardProps) {

  const { data, isFetching, isLoading, error } = useListPatreonUsers()

  const patreonUsername = data?.member?.reduce((carry, current) => {
    return current.username ?? 'Unknown'
  }, 'Unknown')
  const isPatreonCreator = data?.member?.find((ptrUser) => ptrUser.creator) !== undefined

  const isPatreonConnected = !isFetching && data?.member !== undefined
  const isSubscribestarConnected = false
  const subscribestarCreator = false

  return (
    <Card className="" {...props}>
      <CardHeader className="flex flex-col items-start px-4 pb-0 pt-4">
        <p className="text-large">Security Settings</p>
        <p className="text-small text-default-500">Manage your security preferences</p>
      </CardHeader>
      <CardBody className="space-y-2">
        {/* Patreon */}
        <CellWrapper>
          <div>
            <p>Patreon</p>
            <p className="text-small text-default-500">
              The Patreon account linked to your account.
            </p>
          </div>
          <div className="flex w-full flex-wrap items-center justify-around gap-6 sm:w-auto sm:flex-nowrap">
            <div className="flex flex-col items-end">
              {isPatreonConnected ? (<>
                <p>{patreonUsername}</p>
                <p className="text-small text-success">{isPatreonCreator ? 'Creator' : 'User'}</p>
              </>) : <p>Not connected</p>}
            </div>
            {!isPatreonConnected ? <Button
              color={'success'}
              as={Link}
              isLoading={isLoading}
              href={'/connect/patreon?mode=user'}
              radius="full"
              variant="bordered"
            >
              Connect
            </Button> : null}
            {isPatreonConnected && !isPatreonCreator? <Button
              as={Link}
              isLoading={isLoading}
              href={'/connect/patreon?mode=creator'}
              color={'success'}
              radius="full"
              variant="bordered"
            >
              Convert to creator account
            </Button> : null}
          </div>
        </CellWrapper>
        {/* Subscribestar */}
        <CellWrapper>
          <div>
            <p>Subscribestar</p>
            <p className="text-small text-default-500">
              The Subscribestar account linked to your account.
            </p>
          </div>
          <div className="flex w-full flex-wrap items-center justify-around gap-6 sm:w-auto sm:flex-nowrap">
            <div className="flex flex-col items-end">
              {isSubscribestarConnected ? (<>
                <p>{'a'}</p>
                <p className="text-small text-success">{subscribestarCreator ? 'Creator' : 'User'}</p>
              </>) : <p>Not connected</p>}
            </div>
            <Button
              color={'secondary'}
              radius="full"
              variant="bordered"
            >
              Connect
            </Button>
          </div>
        </CellWrapper>
      </CardBody>
    </Card>
  );
}
