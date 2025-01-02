'use client'

import React from 'react'
import { Button, Link } from '@nextui-org/react'
import { Icon } from '@iconify/react'
import Image from 'next/image'
import { useTheme } from 'next-themes'

export default function LoginPage() {
  const { theme } = useTheme()

  return (
    <div className="flex h-full w-full items-center justify-center">
      <div className="flex w-full max-w-sm flex-col gap-4 rounded-large bg-content1 px-8 pb-10 pt-6 shadow-small">
        <p className="pb-2 text-xl font-medium">Log In</p>
        <div className="flex flex-col gap-2">
          <Button
            as={Link}
            startContent={
              <Icon
                icon="ph:patreon-logo"
                width={24}
              />
            }
            variant="bordered"
            href={'/login/patreon'}
          >
            Login with Patreon
          </Button>
          <Button
            as={Link}
            startContent={
              <Image
                width={24}
                height={24}
                src={
                  theme === 'light'
                    ? '/ss_logomark_mono_di_dark.png'
                    : '/ss_logomark_mono_di.png'
                }
                alt={'Subscribestar Logo'}
              />
            }
            variant="bordered"
            href={'/login/subscribestar'}
          >
            Login with Subscribestar
          </Button>
        </div>
      </div>
    </div>
  )
}
