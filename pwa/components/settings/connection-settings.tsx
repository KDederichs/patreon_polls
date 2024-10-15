import type {CardProps} from "@nextui-org/react";

import React from "react";
import {Card, CardHeader, CardBody, Button} from "@nextui-org/react";
import {Icon} from "@iconify/react";
import CellWrapper from "@/components/common/cell-wrapper";


export default function ConnectionSettings(props: CardProps) {
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
              <p>Test Patreon</p>
              <p className="text-small text-success">User</p>
            </div>
            <Button
              color={'danger'}
              radius="full"
              variant="bordered"
            >
              Disconnect
            </Button>
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
              <p>Not yet connected</p>
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
