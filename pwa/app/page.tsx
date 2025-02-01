import { Link } from "@heroui/link";
import { button as buttonStyles } from "@heroui/theme";

import { title, subtitle } from "@/components/primitives";

export default function Home() {
  return (
    <section className="flex flex-col items-center justify-center gap-4 py-8 md:py-10">
      <div className="inline-block max-w-xl text-center justify-center">
        <span className={title()}>Make&nbsp;</span>
        <span className={title({ color: "violet" })}>better&nbsp;</span>
        <br />
        <span className={title()}>
          polls customized for your community
        </span>
        <div className={subtitle({ class: "mt-4" })}>
          Currently supports Patreon and Subscribestar.
        </div>
      </div>

      <div className="flex gap-3">
        <Link
          className={buttonStyles({
            color: "secondary",
            radius: "full",
            variant: "shadow",
          })}
          href={'/login'}
        >
          Login
        </Link>
      </div>
    </section>
  );
}
