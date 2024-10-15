"use client"

import React from "react";
import {Avatar, Button, Spacer, Tab, Tabs, Tooltip, useDisclosure} from "@nextui-org/react";
import {Icon} from "@iconify/react";
import {useMediaQuery} from "usehooks-ts";
import {cn} from "@nextui-org/react";
import AccountSetting from "@/components/settings/account-setting";
import ConnectionSettings from "@/components/settings/connection-settings";



export default function SettingsPage() {
  return (
    <div className="">
      {/* Title */}
      <div className="flex items-center gap-x-3">
        <h1 className="text-3xl font-bold leading-9 text-default-foreground">Settings</h1>
      </div>
      <h2 className="mt-2 text-small text-default-500">
        Customize settings, email preferences, and web appearance.
      </h2>
      {/*  Tabs */}
      <Tabs
        fullWidth
        classNames={{
          base: "mt-6",
          cursor: "bg-content1 dark:bg-content1",
          panel: "w-full p-0 pt-4",
        }}
      >
        <Tab key="account" title="Account">
          <AccountSetting/>
        </Tab>
        <Tab key="connections" title="Connections">
          <ConnectionSettings/>
        </Tab>
      </Tabs>
    </div>
  )
}
