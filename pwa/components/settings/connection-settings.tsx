'use client'

import type { CardProps } from "@heroui/react"

import React from 'react'
import { Card, CardHeader, CardBody, Button } from "@heroui/react"
import { Icon } from '@iconify/react'
import CellWrapper from '@/components/common/cell-wrapper'
import { Link } from "@heroui/link"
import { toast } from 'react-toastify'
import { useListPatreonUsers } from '@/hooks/query/PatreonUser/useListPatreonUsers'
import { useListSubscribestarUser } from '@/hooks/query/SubscribestarUser/useListSubscribestarUser'
import { useConnectResource } from '@/hooks/mutation/Oauth/useConnectResource'
import { useRouter } from 'next/navigation'

export default function ConnectionSettings(props: CardProps) {
  const {
    data: patreonUsers,
    isFetching: patreonUsersFetching,
    isLoading: patreonUsersLoading,
  } = useListPatreonUsers()
  const {
    data: subscribestarUsers,
    isFetching: subscribestarUserFetching,
    isLoading: subscribestarUserLoading,
  } = useListSubscribestarUser()

  const patreonUsername = patreonUsers?.member?.reduce((carry, current) => {
    return current.username ?? 'Unknown'
  }, 'Unknown')
  const isPatreonCreator =
    patreonUsers?.member?.find((ptrUser) => ptrUser.creator) !== undefined
  const subscribeStarUsername = subscribestarUsers?.member?.reduce(
    (carry, current) => {
      return current.username ?? 'Unknown'
    },
    'Unknown',
  )

  const router = useRouter()

  const isPatreonConnected =
    !patreonUsersFetching && patreonUsers?.member?.length !== 0
  const isSubscribestarConnected =
    !subscribestarUserFetching && subscribestarUsers?.member?.length !== 0
  const isSubscribestarCreator =
    subscribestarUsers?.member?.find((ptrUser) => ptrUser.creator) !== undefined

  const oauthConnector = useConnectResource({
    onSuccess: (response) => {
      const redirectUrl = response.redirectUri.replaceAll('%2B', '+')
      console.log(redirectUrl)
      window.location.href = redirectUrl
      return false
    },
    onError: (error) => toast.error(error.message),
  })

  return (
    <Card
      className=""
      {...props}
    >
      <CardHeader className="flex flex-col items-start px-4 pb-0 pt-4">
        <p className="text-large">Security Settings</p>
        <p className="text-small text-default-500">
          Manage your security preferences
        </p>
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
              {isPatreonConnected ? (
                <>
                  <p>{patreonUsername}</p>
                  <p className="text-small text-success">
                    {isPatreonCreator ? 'Creator' : 'User'}
                  </p>
                </>
              ) : (
                <p>Not connected</p>
              )}
            </div>
            {!isPatreonConnected ? (
              <Button
                color={'success'}
                isLoading={patreonUsersLoading || oauthConnector.isPending}
                onPress={() => {
                  oauthConnector.mutate({
                    mode: 'user',
                    uri: '/connect/patreon',
                  })
                }}
                radius="full"
                variant="bordered"
              >
                Connect
              </Button>
            ) : null}
            {isPatreonConnected && !isPatreonCreator ? (
              <Button
                isLoading={patreonUsersLoading || oauthConnector.isPending}
                onPress={() => {
                  oauthConnector.mutate({
                    mode: 'creator',
                    uri: '/connect/patreon',
                  })
                }}
                color={'success'}
                radius="full"
                variant="bordered"
              >
                Convert to creator account
              </Button>
            ) : null}
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
              {isSubscribestarConnected ? (
                <>
                  <p>{subscribeStarUsername}</p>
                  <p className="text-small text-success">
                    {isSubscribestarCreator ? 'Creator' : 'User'}
                  </p>
                </>
              ) : (
                <p>Not connected</p>
              )}
            </div>
            {!isSubscribestarConnected ? (
              <Button
                color={'success'}
                isLoading={subscribestarUserLoading || oauthConnector.isPending}
                onPress={() => {
                  oauthConnector.mutate({
                    mode: 'user',
                    uri: '/connect/subscribestar',
                  })
                }}
                radius="full"
                variant="bordered"
              >
                Connect
              </Button>
            ) : null}
            {isSubscribestarConnected && !isSubscribestarCreator ? (
              <Button
                isLoading={subscribestarUserLoading || oauthConnector.isPending}
                onPress={() => {
                  oauthConnector.mutate({
                    mode: 'creator',
                    uri: '/connect/subscribestar',
                  })
                }}
                color={'success'}
                radius="full"
                variant="bordered"
              >
                Convert to creator account
              </Button>
            ) : null}
          </div>
        </CellWrapper>
      </CardBody>
    </Card>
  )
}
