'use client';

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import Swal from "sweetalert2";
import Link from "next/link";
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/react";

export default function Header() {
    const [userData, setUserData] = useState(null);
    const router = useRouter();

    // Retrieve the admin list from environment variable
    const admins = process.env.NEXT_PUBLIC_ADMINS?.split(',');

    useEffect(() => {
        async function fetchUserData() {
            const response = await fetch("/api/getUserData");
            if (response.status === 401) {
                return;
            }
            const data = await response.json();
            setUserData(data);
        }

        fetchUserData();
    }, []);
    const loggedIn = userData;
    const userName = userData?.login || "";
    const isAdmin = admins?.includes(userName);

    const handleLogout = async () => {
        try {
            // Make a request to the logout API to delete the cookie
            const response = await fetch('/api/logout');
            if (response.ok) {
                Swal.fire({
                    title: "Logged Out",
                    text: "You have been logged out successfully",
                    icon: "success",
                    confirmButtonText: "Close",
                });
                router.push("/");
                window.location.reload(); // Force reload to clear client state
            } else {
                Swal.fire({
                    title: "Error",
                    text: "Failed to log out. Please try again.",
                    icon: "error",
                    confirmButtonText: "Close",
                });
            }
        } catch (error) {
            Swal.fire({
                title: "Error",
                text: "An unexpected error occurred. Please try again.",
                icon: "error",
                confirmButtonText: "Close",
            });
        }
    };    
    

    const handleLogin = () => {
        router.push("/login");
    };

    return (
        <div className="bg-gray-800 w-full text-white">
            <div className="max-w-7xl mx-auto flex items-center justify-between py-3 px-5 2xl:px-0">
                <Link href="/">
                    <img src="/42_evals.png" alt="Logo" className="h-[36px]" />
                </Link>
                <Menu>
                    <MenuButton>
                        <div className="cursor-pointer">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                strokeWidth={1.5}
                                stroke="currentColor"
                                className="size-8"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25"
                                />
                            </svg>
                        </div>
                    </MenuButton>
                    <MenuItems
                        transition
                        anchor="bottom end"
                        className="w-52 origin-top-right rounded-xl border border-sky-500 border-opacity-20 bg-gray-700 bg-opacity-800 p-2 text-sm/6 text-white transition duration-100 ease-out [--anchor-gap:var(--spacing-1)] focus:outline-none data-[closed]:scale-95 data-[closed]:opacity-0"
                    >
                        {loggedIn && (
                            <>
                                <MenuItem>
                                    <Link
                                        href="/profile"
                                        className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                                    >
                                        My Profile
                                    </Link>
                                </MenuItem>
                                {isAdmin && (
                                    <MenuItem>
                                        <Link
                                            href="/admin"
                                            className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                                        >
                                            Admin Panel
                                        </Link>
                                    </MenuItem>
                                )}
                            </>
                        )}
                        <MenuItem>
                            <Link
                                href="/sheets"
                                className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                            >
                                Evaluation Sheets
                            </Link>
                        </MenuItem>
                        <MenuItem>
                            <Link
                                target="_blank"
                                href="https://github.com/rphlr/42-Evals"
                                className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                            >
                                Give this repo a star
                            </Link>
                        </MenuItem>
                        <MenuItem>
                            <Link
                                target="_blank"
                                href="https://github.com/rphlr"
                                className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                            >
                                Follow me on GitHub
                            </Link>
                        </MenuItem>
                        <MenuItem>
                            <Link
                                target="_blank"
                                href="https://www.linkedin.com/in/rphlr"
                                className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                            >
                                Add me on LinkedIn
                            </Link>
                        </MenuItem>
                        <MenuItem>
                            <Link
                                href="/privacy"
                                className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                            >
                                Privacy Policy
                            </Link>
                        </MenuItem>
                        {loggedIn ? (
                            <MenuItem>
                                <button
                                    onClick={handleLogout}
                                    className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                                >
                                    Logout
                                </button>
                            </MenuItem>
                        ) : (
                            <MenuItem>
                                <button
                                    onClick={handleLogin}
                                    className="group flex w-full items-center gap-2 rounded-lg py-1.5 px-3 data-[focus]:bg-white/10"
                                >
                                    Login
                                </button>
                            </MenuItem>
                        )}
                    </MenuItems>
                </Menu>
            </div>
        </div>
    );
}
