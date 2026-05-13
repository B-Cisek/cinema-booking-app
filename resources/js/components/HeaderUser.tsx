import {
    BadgeCheckIcon,
    BellIcon,
    CreditCardIcon,
    LogOutIcon,
    UserRound,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/types';
import { router } from '@inertiajs/react';
import { logout } from '@/routes';

interface HeaderUserProps {
    user: User;
}

export default function HeaderUser({ user }: HeaderUserProps) {
    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="default"
                    size="icon-lg"
                    className="rounded-xl border-border/80 bg-primary/10 text-primary hover:bg-accent/50"
                >
                    <UserRound className="size-6" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel className="space-y-1 normal-case">
                    <span className="block truncate text-sm font-medium text-foreground">
                        { user.email }
                    </span>
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <DropdownMenuGroup>
                    <DropdownMenuItem>
                        <BadgeCheckIcon />
                        Account
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <CreditCardIcon />
                        Billing
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        <BellIcon />
                        Notifications
                    </DropdownMenuItem>
                </DropdownMenuGroup>
                <DropdownMenuSeparator />
                <DropdownMenuItem variant="destructive" onClick={() => router.post(logout())}>
                    <LogOutIcon />
                    Wyloguj
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
