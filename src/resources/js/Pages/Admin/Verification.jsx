import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function Verification() {
    return (
        <div className="min-h-screen bg-slate-900 text-white flex flex-col justify-center items-center">
            <Head title="検証モード" />
            <div className="max-w-md text-center p-8 bg-slate-800 rounded-lg border border-slate-700 shadow-xl">
                <h1 className="text-2xl font-bold text-blue-500 mb-4">検証モード（管理者専用）</h1>
                <p className="text-slate-400 mb-6">
                    このエリアは管理者専用の検証モード画面です。現在準備中です。
                </p>
                <div className="flex justify-between gap-4">
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm font-semibold cursor-pointer"
                    >
                        ログアウト
                    </Link>
                    <Link
                        href="/"
                        className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded text-sm font-semibold"
                    >
                        一般ダッシュボード
                    </Link>
                </div>
            </div>
        </div>
    );
}
