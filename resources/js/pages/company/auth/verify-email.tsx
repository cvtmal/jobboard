import { FormEvent } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { PageProps } from '@/types';

interface VerifyEmailProps extends PageProps {
  status?: string;
}

export default function VerifyEmail({ status }: VerifyEmailProps) {
  const { post, processing } = useForm({});

  const submit = (e: FormEvent) => {
    e.preventDefault();
    post(route('company.verification.send'));
  };

  return (
    <>
      <Head title="Email Verification" />

      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <div className="w-full max-w-md">
          <Card>
            <CardHeader>
              <CardTitle className="text-2xl font-bold text-center">Email Verification</CardTitle>
              <CardDescription className="text-center">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
              </CardDescription>
            </CardHeader>

            <CardContent>
              {status === 'verification-link-sent' && (
                <Alert className="mb-4 bg-green-50 border-green-200">
                  <AlertTitle>Success!</AlertTitle>
                  <AlertDescription>
                    A new verification link has been sent to the email address you provided during registration.
                  </AlertDescription>
                </Alert>
              )}

              <div className="text-center text-sm mt-4 text-gray-600">
                <p>
                  If you didn't receive the email, we'll gladly send you another.
                </p>
              </div>

              <form onSubmit={submit} className="mt-4">
                <Button type="submit" className="w-full" disabled={processing}>
                  Resend Verification Email
                </Button>
              </form>
            </CardContent>

            <CardFooter className="justify-center">
              <form method="POST" action={route('company.logout')}>
                <Button type="submit" variant="ghost" className="text-sm text-gray-600 hover:text-gray-900">
                  Log Out
                </Button>
              </form>
            </CardFooter>
          </Card>
        </div>
      </div>
    </>
  );
}
