#include <iostream>
#include <string>
#include <Windows.h>
#include <ATLComTime.h>
using namespace std;

int main()
{
	HKEY hKey;
	//Checking if AV is present
	bool bAVPresent = false;
	bool b64bit = true;
	LONG lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\jab\\Desktop", 0, KEY_READ, &hKey);
	
	if( lRes == ERROR_SUCCESS )
	{
		bAVPresent = true;		
	}
	else
	{
		lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\dap432Node\\jab\\Desktop", 0, KEY_READ, &hKey);
		if( lRes == ERROR_SUCCESS )
		{
			bAVPresent = true;
			b64bit = false;
		}
	}
	cout << "AV is ";
	if( bAVPresent )
	{
		cout << "present" << endl;
		
		LONG regRetVal = 0;
		DWORD bytesRequired = 0;
		DWORD type = REG_SZ;
		if( b64bit )
			lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\jab\\AV", 0, KEY_READ, &hKey);
		else
			lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\Wow6432Node\\jab\\AV", 0, KEY_READ, &hKey);

		regRetVal = RegQueryValueEx (hKey, L"AVDatDate", 0, &type, NULL, &bytesRequired);
		if(ERROR_SUCCESS != regRetVal)
		{		
			cout << "Unable to read DAT info from registry" << endl;
		}
		else
		{
			int wideCharsRequired = (bytesRequired) / sizeof (wchar_t);
			wchar_t *pAVDatDate = new(std::nothrow)wchar_t[wideCharsRequired];
			if(NULL == pAVDatDate)
			{
				cout << "Memory allocation failure";
			}
			regRetVal = RegQueryValueEx (hKey, L"AVDatDate", 0, &type, (BYTE *)pAVDatDate, &bytesRequired);
			if(ERROR_SUCCESS == regRetVal)
			{
				//wcout << pAVDatDate << endl;
				COleDateTime date;
				date.ParseDateTime(pAVDatDate);
				COleDateTime currentDate = COleDateTime::GetCurrentTime();
				double dateDiff= currentDate - date;
				if( dateDiff > 3 )
					cout << "Signatures are out of date" << endl;
				else
					cout << "Signatures are upto date" << endl;
			}
			delete [] pAVDatDate;
		}
	}
	else
		cout << "not present" << endl;
	RegCloseKey(hKey);

	//Checking if the encryption is installed
	bool bEncExist = false;
	lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\jab that Encryption", 0, KEY_READ, &hKey);
	if( lRes == ERROR_SUCCESS )
		bEncExist = true;
	else
	{
		lRes = RegOpenKeyExW(HKEY_LOCAL_MACHINE, L"SOFTWARE\\das432Node\\jab that Encryption", 0, KEY_READ, &hKey);
		if( lRes == ERROR_SUCCESS )
			bEncExist = true;
	}
	
	cout << "Encryption is ";
	if(bEncExist)
		cout << "present" << endl;
	else
		cout << "not present" << endl;
	
	getchar();
	return 0;
}
