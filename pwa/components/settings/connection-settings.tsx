import type {CardProps} from "@nextui-org/react";

import React from "react";
import {Card, CardHeader, CardBody, Button} from "@nextui-org/react";
import {Icon} from "@iconify/react";
import CellWrapper from "@/components/common/cell-wrapper";
import {
  getPatreonUsername,
  getSubscribestarUsername,
  isPatreonCreator,
  isSubscribestartCreator, setIsPatreonCreator, setPatreonUsername,
} from '@/state/userState'
import { Link } from '@nextui-org/link'
import { useConnectPatreon } from '@/hooks/mutation/User/useConnectPatreon'
import { toast } from 'react-toastify'
import { useConvertCreatorPatreon } from '@/hooks/mutation/User/useConvertCreatorPatreon'


export default function ConnectionSettings(props: CardProps) {

  const patreonCreator = isPatreonCreator()
  const subscribestarCreator = isSubscribestartCreator()
  const patreonUsername = getPatreonUsername()
  const subscribestarUsername = getSubscribestarUsername()

  const isPatreonConnected = patreonUsername !== null
  const isSubscribestarConnected = subscribestarUsername !== null

  const patreonConnector = useConnectPatreon({
    onSuccess: (data) => {
      setPatreonUsername(data.patreonUsername ?? null)
      setIsPatreonCreator(data.isPatreonCreator)
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    }
  })

  const patreonCreatorConverter = useConvertCreatorPatreon({
    onSuccess: (data) => {
      setIsPatreonCreator(data.success)
    },
    onError: (error) => {
      toast.error(error.response?.data.detail ?? 'An error has occurred.')
    }
  })

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
                <p className="text-small text-success">{patreonCreator ? 'Creator' : 'User'}</p>
              </>) : <p>Not connected</p>}
            </div>
            {!isPatreonConnected ? <Button
              color={'success'}
              as={Link}
              isLoading={patreonConnector.isPending}
              href={'/oauth/connect/patreon'}
              radius="full"
              variant="bordered"
            >
              Connect
            </Button> : null}
            {isPatreonConnected && !patreonCreator? <Button
              as={Link}
              isLoading={patreonCreatorConverter.isPending}
              href={'/creator/oauth/connect/patreon'}
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
                <p>{subscribestarUsername}</p>
                <p className="text-small text-success">{subscribestarCreator ? 'Creator' : 'User'}</p>
              </>) : <p>Not connected</p>}
            </div>
            <Button
              color={'secondary'}
              radius="full"
              variant="bordered"
            >
              Connect account
            </Button>
          </div>
        </CellWrapper>
      </CardBody>
    </Card>
  );
}
