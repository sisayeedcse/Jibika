import type { Metadata } from "next";
import { Inter, Noto_Sans_Bengali } from "next/font/google";
import "./globals.css";
import { I18nProvider } from "@/lib/i18n";
import { Header } from "@/components/layout/header";

const inter = Inter({ subsets: ["latin"], variable: "--font-inter" });
const notoSansBengali = Noto_Sans_Bengali({ subsets: ["bengali"], variable: "--font-noto-bengali" });

export const metadata: Metadata = {
  title: "JIBIKA | From Small Capital to Productive Livelihood",
  description: "AI-Powered FinTech for Inclusive Livelihood & Productive Asset Financing",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${inter.variable} ${notoSansBengali.variable} font-sans antialiased min-h-screen bg-slate-50 text-slate-900`}>
        <I18nProvider>
          <div className="relative flex min-h-screen flex-col">
            <Header />
            <main className="flex-1">{children}</main>
          </div>
        </I18nProvider>
      </body>
    </html>
  );
}
