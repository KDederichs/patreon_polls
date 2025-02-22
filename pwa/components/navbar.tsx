'use client'
import {
  Navbar as NextUINavbar,
  NavbarContent,
  NavbarMenu,
  NavbarMenuToggle,
  NavbarBrand,
  NavbarItem,
  NavbarMenuItem,
} from "@heroui/navbar"
import { Link } from "@heroui/link"
import { link as linkStyles } from "@heroui/theme"
import NextLink from 'next/link'
import clsx from 'clsx'

import { siteConfig } from '@/config/site'
import { ThemeSwitch } from '@/components/theme-switch'
import { GithubIcon, Logo } from '@/components/icons'
import { useAuthStore } from '@/state/authState'

export const Navbar = () => {
  const isAuthenticated = useAuthStore((state) => state.token !== null)

  return (
    <NextUINavbar
      maxWidth="xl"
      position="sticky"
    >
      <NavbarContent
        className="basis-1/5 sm:basis-full"
        justify="start"
      >
        <NavbarBrand
          as="li"
          className="max-w-fit gap-3"
        >
          <NextLink
            className="flex items-center justify-start gap-1"
            href="/"
          >
            <p className="font-bold text-inherit">Better Polls</p>
          </NextLink>
        </NavbarBrand>
        <ul className="ml-2 hidden justify-start gap-4 lg:flex">
          {siteConfig.navItems
            .filter((item) => item.requiresAuth === isAuthenticated)
            .map((item) => (
              <NavbarItem key={item.href}>
                <NextLink
                  suppressHydrationWarning
                  className={clsx(
                    linkStyles({ color: 'foreground' }),
                    'data-[active=true]:font-medium data-[active=true]:text-primary',
                  )}
                  color="foreground"
                  href={item.href}
                >
                  {item.label}
                </NextLink>
              </NavbarItem>
            ))}
        </ul>
      </NavbarContent>

      <NavbarContent
        className="hidden basis-1/5 sm:flex sm:basis-full"
        justify="end"
      >
        <NavbarItem className="hidden gap-2 sm:flex">
          <Link
            isExternal
            aria-label="Github"
            href={siteConfig.links.github}
          >
            <GithubIcon className="text-default-500" />
          </Link>
          <ThemeSwitch />
        </NavbarItem>
      </NavbarContent>

      <NavbarContent
        className="basis-1 pl-4 sm:hidden"
        justify="end"
      >
        <Link
          isExternal
          aria-label="Github"
          href={siteConfig.links.github}
        >
          <GithubIcon className="text-default-500" />
        </Link>
        <ThemeSwitch />
        <NavbarMenuToggle />
      </NavbarContent>

      <NavbarMenu>
        <div className="mx-4 mt-2 flex flex-col gap-2">
          {siteConfig.navItems
            .filter((item) => item.requiresAuth === isAuthenticated)
            .map((item, index) => (
              <NavbarMenuItem key={`${item}-${index}`}>
                <Link
                  href={item.href}
                  size="lg"
                  suppressHydrationWarning
                >
                  {item.label}
                </Link>
              </NavbarMenuItem>
            ))}
        </div>
      </NavbarMenu>
    </NextUINavbar>
  )
}
