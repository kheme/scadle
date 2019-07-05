<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use FileUploader;

class ScanController extends Controller{
    public function DoUpload(request $request){
        if($request->ajax()){
            // Destination folder for uploaded files
            $folder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "codes" . DIRECTORY_SEPARATOR;
            
            // create file with list of uploaded files
            $listFile   = $folder.$request->rand."_list.txt";
            $uploadedL  = fopen($listFile, 'a+');

            // loop through selected files and upload
            $file       = $request->file('files')[0];
            // generate new name for uploaded files
            //$newName = str_replace(' ','_',$file->getClientOriginalName());
            $underscore = str_replace(' ','_',$file->getClientOriginalName());
            $newName    = $request->rand . '_' . $underscore;

            // if a file existed before, delete from destination folder
            if(file_exists("$folder$newName")){
                unlink("$folder$newName");
            }

            // only upload files that are valid, and of the selected "file type filter"
            if ($file->isValid() && $file->getClientOriginalExtension() == $request->ext) {
                $file->move($folder, $newName);
                
                // make list of uploaded files
                //$uploaded[] = "$folder$newName";
                fwrite($uploadedL, "$newName\n");
            }

            // close file with list of uploaded files
            fclose($uploadedL);
        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function DoScan(request $request){
        // Destination folder for uploaded files
        $folder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "storage" . DIRECTORY_SEPARATOR . "codes" . DIRECTORY_SEPARATOR;

        // loop through selected files and upload
        foreach($request->file('files') as $file){
            // generate new name for uploaded files
            $newName = str_replace(' ','_',$file->getClientOriginalName());

            // if a file existed before, delete from destination folder
            if(file_exists("$folder$newName")){
                unlink("$folder$newName");
            }

            // only upload files that are valid, and of the selected "file type filter"
            if ($file->isValid() && $file->getClientOriginalExtension() == $request->ext) {
                $file->move($folder, $newName);
                
                // make list of uploaded files
                $uploaded[] = "$folder$newName";
            }
        }

        // pre-process files
        foreach($uploaded as $file){
            // read file content
            if(file_exists($file)){
                $handle = fopen($file, "r+");
                $code   = fread($handle, filesize($file));

                // get file name only, excluding the directory
                $fname  = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1);

                // get file extention
                $ext    = substr($file, strrpos($file,'.') + 1, 5);

                // define keywords
                switch ($ext){
                    case 'php':
                        $keywords = array('/\b__halt_compiler\b/ui', '/\b__CLASS__\b/ui', '/\b__DIR__\b/ui', '/\b__FILE__\b/ui', '/\b__FUNCTION__\b/ui', '/\b__LINE__\b/ui', '/\b__METHOD__\b/ui', '/\b__NAMESPACE__\b/ui', '/\b__TRAIT__\b/ui', '/\babstract\b/ui', '/\band\b/ui', '/\barray\b/ui', '/\bas\b/ui', '/\bbreak\b/ui', '/\bcallable\b/ui', '/\bcase\b/ui', '/\bcatch\b/ui', '/\bclass\b/ui', '/\bclone\b/ui', '/\bconst\b/ui', '/\bcontinue\b/ui', '/\bdeclare\b/ui', '/\bdefault\b/ui', '/\bdie\b/ui', '/\bdo\b/ui', '/\becho\b/ui', '/\belse\b/ui', '/\belseif\b/ui', '/\bempty\b/ui', '/\benddeclare\b/ui', '/\bendfor\b/ui', '/\bendforeach\b/ui', '/\bendif\b/ui', '/\bendswitch\b/ui', '/\bendwhile\b/ui', '/\beval\b/ui', '/\bexit\b/ui', '/\bextends\b/ui', '/\bfinal\b/ui', '/\bfor\b/ui', '/\bforeach\b/ui', '/\bfunction\b/ui', '/\bglobal\b/ui', '/\bgoto\b/ui', '/\bif\b/ui', '/\bimplements\b/ui', '/\binclude\b/ui', '/\binclude_once\b/ui', '/\binstanceof\b/ui', '/\binsteadof\b/ui', '/\binterface\b/ui', '/\bisset\b/ui', '/\blist\b/ui', '/\bnamespace\b/ui', '/\bnew\b/ui', '/\bor\b/ui', '/\bprint\b/ui', '/\bprivate\b/ui', '/\bprotected\b/ui', '/\bpublic\b/ui', '/\brequire\b/ui', '/\brequire_once\b/ui', '/\breturn\b/ui', '/\bstatic\b/ui', '/\bswitch\b/ui', '/\bthrow\b/ui', '/\btrait\b/ui', '/\btry\b/ui', '/\bunset\b/ui', '/\buse\b/ui', '/\bvar\b/ui', '/\bwhile\b/ui', '/\bxor\b/ui');
                        break;
                    case 'cpp':
                        $keywords = array('/\bauto\b/ui', '/\bconst\b/ui', '/\bdouble\b/ui', '/\bfloat\b/ui', '/\bint\b/ui', '/\bshort\b/ui', '/\bstruct\b/ui', '/\bunsigned\b/ui', '/\bbreak\b/ui', '/\bcontinue\b/ui', '/\belse\b/ui', '/\bfor\b/ui', '/\blong\b/ui', '/\bsigned\b/ui', '/\bswitch\b/ui', '/\bvoid case\b/ui', '/\bdefault\b/ui', '/\benum\b/ui', '/\bgoto\b/ui', '/\bregister\b/ui', '/\bsizeof\b/ui', '/\btypedef\b/ui', '/\bvolatile\b/ui', '/\bchar\b/ui', '/\bdo\b/ui', '/\bextern\b/ui', '/\bif\b/ui', '/\breturn\b/ui', '/\bstatic\b/ui', '/\bunion\b/ui', '/\bwhile\b/ui', '/\basm\b/ui', '/\bdynamic_cast\b/ui', '/\bnamespace\b/ui', '/\breinterpret_cast\b/ui', '/\btry\b/ui', '/\bbool\b/ui', '/\bexplicit\b/ui', '/\bnew\b/ui', '/\bstatic_cast\b/ui', '/\btypeid\b/ui', '/\bcatch\b/ui', '/\bfalse\b/ui', '/\boperator\b/ui', '/\btemplate\b/ui', '/\btypename\b/ui', '/\bclass\b/ui', '/\bfriend\b/ui', '/\bprivate\b/ui', '/\bthis\b/ui', '/\busing\b/ui', '/\bconst_cast\b/ui', '/\binline\b/ui', '/\bpublic\b/ui', '/\bthrow\b/ui', '/\bvirtual\b/ui', '/\bdelete\b/ui', '/\bmutable\b/ui', '/\bprotected\b/ui', '/\btrue\b/ui', '/\bwchar_t\b/ui', '/\band\b/ui', '/\bbitand\b/ui', '/\bcompl\b/ui', '/\bnot_eq\b/ui', '/\bor_eq\b/ui', '/\bxor_eq\b/ui', '/\band_eq\b/ui', '/\bbitor\b/ui', '/\bnot\b/ui', '/\bor\b/ui', '/\bxor\b/ui', '/\bcin\b/ui', '/\bendl\b/ui', '/\bINT_MIN\b/ui', '/\biomanip\b/ui', '/\bmain\b/ui', '/\bnpos\b/ui', '/\bstd\b/ui', '/\bcout\b/ui', '/\binclude\b/ui', '/\bINT_MAX\b/ui', '/\biostream\b/ui', '/\bMAX_RAND\b/ui', '/\bNULL\b/ui', '/\bstring\b/ui');
                        break;
                    case 'asm':
                        $keywords = array('/\bDF\b/ui', '/\bGROUP\b/ui', '/\bORG\b/ui', '/\bDGROUP\b/ui', '/\bGT\b/ui', '/\b%OUT\b/ui', '/\bDOSSEG\b/ui', '/\bHIGH\b/ui', '/\bPAGE\b/ui', '/\bDQ\b/ui', '/\bIF\b/ui', '/\bPARA\b/ui', '/\bDS\b/ui', '/\bIF1\b/ui', '/\bPROC\b/ui', '/\bDT\b/ui', '/\bIF2\b/ui', '/\bPTR\b/ui', '/\bDUP\b/ui', '/\bIFB\b/ui', '/\bPUBLIC\b/ui', '/\bDW\b/ui', '/\bIFDEF\b/ui', '/\bPURGE\b/ui', '/\bDWORD\b/ui', '/\bIFGIF\b/ui', '/\bQWORD\b/ui', '/\b.186\b/ui', '/\bELSE\b/ui', '/\bIFDE\b/ui', '/\b.RADIX\b/ui', '/\b.286\b/ui', '/\bEND\b/ui', '/\bIFIDN\b/ui', '/\bRECORD\b/ui', '/\b.286P\b/ui', '/\bENDIF\b/ui', '/\bIFNB\b/ui', '/\bREPT\b/ui', '/\b.287\b/ui', '/\bENDM\b/ui', '/\bIFNDEF\b/ui', '/\b.SALL\b/ui', '/\b.386\b/ui', '/\bENDP\b/ui', '/\bINCLUDE\b/ui', '/\bSEG\b/ui', '/\b.386P\b/ui', '/\bENDS\b/ui', '/\bINCLUDELIB\b/ui', '/\bSEGMENT\b/ui', '/\b.387\b/ui', '/\bEQ\b/ui', '/\bIRP\b/ui', '/\b.SEQ\b/ui', '/\b.8086\b/ui', '/\bEQU\b/ui', '/\bIRPC\b/ui', '/\b.SFCOND\b/ui', '/\b.8087\b/ui', '/\b.ERR\b/ui', '/\bLABEL\b/ui', '/\bSHL\b/ui', '/\bALIGN\b/ui', '/\b.ERR1\b/ui', '/\b.LALL\b/ui', '/\bSHORT\b/ui', '/\b.ALPHA\b/ui', '/\b.ERR2\b/ui', '/\bLARGE\b/ui', '/\bSHR\b/ui', '/\bAND\b/ui', '/\b.ERRB\b/ui', '/\bLE\b/ui', '/\bSIZE\b/ui', '/\bASSUME\b/ui', '/\b.ERRDEF\b/ui', '/\bLENGTH\b/ui', '/\bSMALL\b/ui', '/\bAT\b/ui', '/\b.ERRDIF\b/ui', '/\b.LFCOND\b/ui', '/\bSTACK\b/ui', '/\bBYTE\b/ui', '/\b.ERRE\b/ui', '/\b.LIST\b/ui', '/\b@STACK\b/ui', '/\b.CODE\b/ui', '/\b.ERRIDN\b/ui', '/\bLOCAL\b/ui', '/\b.STACK\b/ui', '/\b@CODE\b/ui', '/\b.ERRNB\b/ui', '/\bLOW\b/ui', '/\bSTRUC\b/ui', '/\b@CODESIZE\b/ui', '/\b.ERRNDEF\b/ui', '/\bLT\b/ui', '/\bSUBTTL\b/ui', '/\bCOMM\b/ui', '/\b.ERRNZ\b/ui', '/\bMACRO\b/ui', '/\bTBYTE\b/ui', '/\bCOMMENT\b/ui', '/\bEVEN\b/ui', '/\bMASK\b/ui', '/\b.TFCOND\b/ui', '/\b.CONST\b/ui', '/\bEXITM\b/ui', '/\bMEDIUM\b/ui', '/\bTHIS\b/ui', '/\b.CREF\b/ui', '/\bEXTRN\b/ui', '/\bMOD\b/ui', '/\bTITLE\b/ui', '/\b@CURSEG\b/ui', '/\bFAR\b/ui', '/\b.MODEL\b/ui', '/\bTYPE\b/ui', '/\b@DATA\b/ui', '/\b@FARDATA\b/ui', '/\bNAME\b/ui', '/\b.TYPE\b/ui', '/\b.DATA\b/ui', '/\b.FARDATA\b/ui', '/\bNE\b/ui', '/\bWIDTH\b/ui', '/\b@DATA?\b/ui', '/\b@FARDATA?\b/ui', '/\bNEAR\b/ui', '/\bWORD\b/ui', '/\b.DATA?\b/ui', '/\b.FARDATA?\b/ui', '/\bNOT\b/ui', '/\b@WORDSIZE\b/ui', '/\b@DATASIZE\b/ui', '/\b@FILENAME\b/ui', '/\bNOTHING\b/ui', '/\b.XALL\b/ui', '/\bDB\b/ui', '/\bFWORD\b/ui', '/\bOFFSET\b/ui', '/\b.XCREP,\b/ui', '/\bDD,\b/ui', '/\bGE,\b/ui', '/\bOR,\b/ui', '/\b.XLIST,\b/ui', '/\bXOR\b/ui');
                        break;
                    case 'asp':
                        $keywords = array('/\b@CODEPAGE\b/ui', '/\b@ENABLESESSIONSTATE\b/ui', '/\b@LANGUAGE\b/ui', '/\b@LCID\b/ui', '/\b@TRANSACTION"\b/ui', '/\bAbandon\b/ui', '/\bAddHeader\b/ui', '/\bAppendToLog\b/ui', '/\bApplication\b/ui', '/\bApplication_OnEnd\b/ui', '/\bApplication_OnStart\b/ui', '/\bASPCode\b/ui', '/\bASPDescription\b/ui', '/\bASPError\b/ui', '/\bBinaryRead\b/ui', '/\bBinaryWrite\b/ui', '/\bBuffer\b/ui', '/\bCacheControl\b/ui', '/\bCategory\b/ui', '/\bCharset\b/ui', '/\bClear\b/ui', '/\bClientCertificate\b/ui', '/\bCodePage\b/ui', '/\bCodePage\b/ui', '/\bColumn\b/ui', '/\bContents\b/ui', '/\bContents\b/ui', '/\bContentType\b/ui', '/\bCookies\b/ui', '/\bCookies\b/ui', '/\bCreateObject\b/ui', '/\bDescription\b/ui', '/\bEnd\b/ui', '/\bExecute\b/ui', '/\bExpires\b/ui', '/\bExpiresAbsolute\b/ui', '/\bFile\b/ui', '/\bFlush\b/ui', '/\bForm\b/ui', '/\bGetLastError\b/ui', '/\bHTMLEncode\b/ui', '/\bIsClientConnected\b/ui', '/\bLCID\b/ui', '/\bLCID\b/ui', '/\bLine\b/ui', '/\bLock\b/ui', '/\bMapPath\b/ui', '/\bNumber\b/ui', '/\bObjectContext\b/ui', '/\bOnEndPage\b/ui', '/\bOnStartPage\b/ui', '/\bOnTransactionAbort\b/ui', '/\bOnTransactionCommit\b/ui', '/\bPICS\b/ui', '/\bQueryString\b/ui', '/\bRedirect\b/ui', '/\bRemove\b/ui', '/\bRemove\b/ui', '/\bRemoveAll\b/ui', '/\bRemoveAll\b/ui', '/\bRequest\b/ui', '/\bResponse\b/ui', '/\bScriptTimeout\b/ui', '/\bServer\b/ui', '/\bServerVariables\b/ui', '/\bSession\b/ui', '/\bSession_OnEnd\b/ui', '/\bSession_OnStart\b/ui', '/\bSessionID\b/ui', '/\bSetAbort\b/ui', '/\bSetComplete\b/ui', '/\bSource\b/ui', '/\bStaticObjects\b/ui', '/\bStaticObjects\b/ui', '/\bStatus\b/ui', '/\bTimeout\b/ui', '/\bTotalBytes\b/ui', '/\bTransfer\b/ui', '/\bUnlock\b/ui', '/\bURLEncode\b/ui', '/\bWrite\b/ui', '/\bPrintShare\b/ui');
                        break;
                    case 'vb':
                        $keywords = array('/\b#Const\b/ui', '/\b#Else\b/ui', '/\b#ElseIf\b/ui', '/\b#End\b/ui', '/\b#If\b/ui', '/\bAddHandler\b/ui', '/\bAddressOf\b/ui', '/\bAlias\b/ui', '/\bAnd\b/ui', '/\bAndAlso\b/ui', '/\bAs\b/ui', '/\bBoolean\b/ui', '/\bByRef\b/ui', '/\bByte\b/ui', '/\bByVal\b/ui', '/\bCall\b/ui', '/\bCase\b/ui', '/\bCatch\b/ui', '/\bCBool\b/ui', '/\bCByte\b/ui', '/\bCChar\b/ui', '/\bCDate\b/ui', '/\bCDbl\b/ui', '/\bCDec\b/ui', '/\bChar\b/ui', '/\bCInt\b/ui', '/\bClass Constraint\b/ui', '/\bClass Statement\b/ui', '/\bCLng\b/ui', '/\bCObj\b/ui', '/\bConst\b/ui', '/\bContinue\b/ui', '/\bCSByte\b/ui', '/\bCShort\b/ui', '/\bCSng\b/ui', '/\bCStr\b/ui', '/\bCType\b/ui', '/\bCUInt\b/ui', '/\bCULng\b/ui', '/\bCUShort\b/ui', '/\bDate\b/ui', '/\bDecimal\b/ui', '/\bDeclare\b/ui', '/\bDefault\b/ui', '/\bDelegate\b/ui', '/\bDim\b/ui', '/\bDirectCast\b/ui', '/\bDo\b/ui', '/\bDouble\b/ui', '/\bEach\b/ui', '/\bElse\b/ui', '/\bElseIf\b/ui', '/\bEnd\b/ui', '/\bEnd Statement\b/ui', '/\bEndIf\b/ui', '/\bEnum\b/ui', '/\bErase\b/ui', '/\bError\b/ui', '/\bEvent\b/ui', '/\bExit\b/ui', '/\bFinally\b/ui', '/\bFor\b/ui', '/\bFriend\b/ui', '/\bFunction\b/ui', '/\bGet\b/ui', '/\bGetType\b/ui', '/\bGetXMLNamespace\b/ui', '/\bGlobal\b/ui', '/\bGoSub\b/ui', '/\bGoTo\b/ui', '/\bHandles\b/ui', '/\bIf\b/ui', '/\bImplements\b/ui', '/\bImplements Statement\b/ui', '/\bImports\b/ui', '/\bIn\b/ui', '/\bInherits\b/ui', '/\bInteger\b/ui', '/\bInterface\b/ui', '/\bIs\b/ui', '/\bIsNot\b/ui', '/\bLet\b/ui', '/\bLib\b/ui', '/\bLike\b/ui', '/\bLong\b/ui', '/\bLoop\b/ui', '/\bMe\b/ui', '/\bMod\b/ui', '/\bModule\b/ui', '/\bModule Statement\b/ui', '/\bMustInherit\b/ui', '/\bMustOverride\b/ui', '/\bMyBase\b/ui', '/\bMyClass\b/ui', '/\bNamespace\b/ui', '/\bNarrowing\b/ui', '/\bNew Constraint\b/ui', '/\bNew Operator\b/ui', '/\bNext\b/ui', '/\bNot\b/ui', '/\bNothing\b/ui', '/\bNotInheritable\b/ui', '/\bNotOverridable\b/ui', '/\bObject\b/ui', '/\bOf\b/ui', '/\bOn\b/ui', '/\bOperator\b/ui', '/\bOption\b/ui', '/\bOptional\b/ui', '/\bOr\b/ui', '/\bOrElse\b/ui', '/\bOut\b/ui', '/\bOverloads\b/ui', '/\bOverridable\b/ui', '/\bOverrides\b/ui', '/\bParamArray\b/ui', '/\bPartial\b/ui', '/\bPrivate\b/ui', '/\bProperty\b/ui', '/\bProtected\b/ui', '/\bPublic\b/ui', '/\bRaiseEvent\b/ui', '/\bReadOnly\b/ui', '/\bReDim\b/ui', '/\bREM\b/ui', '/\bRemoveHandler\b/ui', '/\bResume\b/ui', '/\bReturn\b/ui', '/\bSByte\b/ui', '/\bSelect\b/ui', '/\bSet\b/ui', '/\bShadows\b/ui', '/\bShared\b/ui', '/\bShort\b/ui', '/\bSingle\b/ui', '/\bStatic\b/ui', '/\bStep\b/ui', '/\bStop\b/ui', '/\bString\b/ui', '/\bStructure Constraint\b/ui', '/\bStructure Statement\b/ui', '/\bSub\b/ui', '/\bSyncLock\b/ui', '/\bThen\b/ui', '/\bThrow\b/ui', '/\bTo\b/ui', '/\bTry\b/ui', '/\bTryCast\b/ui', '/\bTypeOf\b/ui', '/\bUInteger\b/ui', '/\bULong\b/ui', '/\bUShort\b/ui', '/\bUsing\b/ui', '/\bVariant\b/ui', '/\bWend\b/ui', '/\bWhen\b/ui', '/\bWhile\b/ui', '/\bWidening\b/ui', '/\bWith\b/ui', '/\bWithEvents\b/ui', '/\bWriteOnly\b/ui', '/\bXor\b/ui', '/\bFALSE\b/ui', '/\bTRUE\b/ui');
                        break;
                }

                // define tokens
                $token['keyword']   = 1;
                $token['identifier']= 2;
                $token['separator'] = 3;
                
                // remove comments like # this is a comment
                $code = preg_replace('/#.*/', '', $code);

                // remove comments like // this is a comment
                $code = preg_replace('/\/\/.*/', "\n", $code);

                // remove comments like /* this is a comment */
                $code = preg_replace('/(\/\*)([\r\n])*[\s\S]*?(\*\/)/', '', $code);

                // replace numbers (an identifier)
                $code = preg_replace('/\b[0-9]+/', ' 2 ', $code );

                // replace words within quotation marks (an identifier)
                $code = preg_replace('/(“[^“|^”]+“|“[^“|^”]+”|”[^“|^”]+”|\'[^\']+\'|\"[^\"]+\")/', ' 2 ', $code);

                // replace dollar signed variable names (an identifier)
                $code = preg_replace('/\$[_a-zA-Z0-9]+/u', ' 2 ', $code);

                // replace keywords in code
                $code   = preg_replace($keywords, ' 1 ', $code);

                // replace separators
                $code = preg_replace ('/[^\w_$\t\s]/u', '', $code);

                // replace all other words left as identifiers
                $code = preg_replace('/[^0-9 ]+/u', ' 2 ', $code);

                // replace multiple spaces
                $code = preg_replace ('/\s+/imu', ' ', $code);

                // split into array
                $code = array_filter(explode(' ', $code));

                for($i = 0; $i <= (count($code) - $request->level); $i++){
                    $tokens[$fname][] = implode(',',array_slice($code, $i, $request->level));
                }
            }
        }

        // compare code tokens
        foreach($tokens as $key1=>$tok1){
            // make a list of checked codes, so we can skip and not compare a code with itself
            $checked[] = $key1;

            foreach($tokens as $key2=>$tok2){
                // do not compare a code's tokens with itself
                if(array_search($key2, $checked) === false){
                    // merge the tokens of both codes
                    $total = array_merge($tok1, $tok2);
                    
                    // get distinct tokens from merged tokens
                    $dist = array_unique($total);

                    // find distinct tokens common to the tokens from both codes
                    $found = array_intersect($dist, $tok1, $tok2);

                    // calculate similarity using Jaccard's coefficient
                    $results["$key1 - $key2"] = round((count($found)  /  count($dist)) * 100, 1);
                }
            }
        }
        //return "Success";
        return view('home', ['results' => $results]);
    }
}
